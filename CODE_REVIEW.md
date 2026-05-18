# Code Review — it-holding (Laravel)

> Revue réalisée le 2026-05-18 | Périmètre : contrôleurs, modèles, routes, seeders, middleware

---

## 🟢 Points forts

- **Structure MVC propre** : bonne séparation entre contrôleurs Admin et frontend.
- **Throttling sur les routes sensibles** : login (×5/min), register (×3/min), contact (×5/min). ✅
- **Transactions DB dans `placeOrder`** : rollback correct en cas d'erreur. ✅
- **Suppression des fichiers physiques** sur `destroy` dans `ProductController`. ✅
- **Casts Eloquent cohérents** : prix en `decimal:2`, booléens bien castés. ✅
- **Middleware admin fonctionnel** : `EnsureUserIsAdmin` simple et correct. ✅
- **Régénération de session à la connexion** : protection CSRF/fixation de session OK. ✅

---

## 🔴 Bugs / Problèmes critiques

### 1. `placeOrder` — Redirect vers `$order->id` au lieu de la route nommée avec l'objet

```php
// web.php (ligne 31)
Route::get('/thanks/{order}', [ShopController::class, 'thanks'])->name('shop.thanks');

// ShopController.php (ligne 253)
return redirect()->route('shop.thanks', $order->id);

// ShopController.php (ligne 268) — la méthode thanks() prend $orderId
public function thanks($orderId) { ... }
```

> ⚠️ La route utilise `{order}` (Route Model Binding potentiel) mais la méthode reçoit `$orderId` en entier.
> C'est fonctionnel aujourd'hui, mais **incohérent** — si quelqu'un passe à du Route Model Binding plus tard, ça cassera. Uniformiser.

---

### 2. `QuoteController::store` / `update` — Utilisation de `$request->xxx` au lieu de `$validated`

```php
// ❌ Après validate(), les données raw de $request sont réutilisées
$quote = Quote::create([
    'number'      => $request->number,       // devrait être $validated['number']
    'client_name' => $request->client_name,
    ...
]);
```

> **Même problème dans `InvoiceController`.**
> Cela contourne partiellement la validation — si un champ est nommé différemment ou transformé,
> vous travaillez sur des données non-validées. **Toujours utiliser `$validated`.**

---

### 3. `QuoteController::convert` — Invoice créée avec `status = 'paid'` par défaut

```php
// ligne 220
'status' => 'paid', // Or draft
```

> Le commentaire dit "Or draft" mais le code force `'paid'`. Une facture convertie d'un devis
> accepté ne devrait pas être automatiquement marquée comme payée. Cela fausse les stats de
> paiement. **Changer en `'draft'` ou `'sent'`.**

---

### 4. `QuoteController::convert` — Pas de transaction DB

La conversion crée une `Invoice` + N `InvoiceItem` + met à jour le statut du `Quote`, le tout
**sans `DB::transaction()`**. Si la création d'un `InvoiceItem` échoue à mi-parcours, vous vous
retrouvez avec une facture incomplète et un devis marqué `'converted'`.

```php
// ❌ Manque un DB::transaction() autour de tout ce bloc
$invoice = Invoice::create([...]);
foreach ($quote->items as $item) {
    $invoice->items()->create([...]);
}
$quote->update(['status' => 'converted']);
```

---

### 5. `DashboardController::index` — Doublon dans `whereIn`

```php
// ligne 20 — 'processing' apparaît deux fois
$pendingOrders = $user->orders()
    ->whereIn('status', ['pending', 'processing', 'in_progress', 'processing'])
    ->count();
```

> Sans impact fonctionnel mais c'est un signe d'inattention. Nettoyer.

---

### 6. `ShopController::removeFromCart` — Pas de fallback si le panier est `null`

```php
public function removeFromCart($id)
{
    $cart = Session::get('cart'); // ← peut retourner null
    if (isset($cart[$id])) {
        unset($cart[$id]);
        Session::put('cart', $cart); // ← met null en session si $cart était null
    }
    ...
}
```

> Utiliser `Session::get('cart', [])` comme partout ailleurs pour la cohérence.

---

### 7. `ProductController` — Slug toujours recalculé même si le nom n'a pas changé

```php
// update() ligne 146
$validated['slug'] = $this->generateUniqueSlug($request->name, $product->id);
```

> Le slug est **toujours recalculé** même si le nom n'a pas changé. Pour un produit déjà
> référencé (URLs externes, SEO), cela peut modifier le slug à chaque mise à jour.
> Vérifier d'abord si `$request->name !== $product->name`.

---

### 8. `DatabaseSeeder` — `AdminUserSeeder` non appelé

```php
$this->call([
    CategorySeeder::class,
    BrandSeeder::class,
    // ❌ AdminUserSeeder::class n'est pas appelé
]);
```

> `AdminUserSeeder.php` existe mais n'est pas inclus dans le `DatabaseSeeder`. En environnement
> frais (`php artisan db:seed`), il n'y aura pas d'administrateur.

---

## 🟡 Problèmes de sécurité / bonnes pratiques

### 9. Routes de partage public — Pas d'expiration ni révocation du token

```php
// web.php (lignes 83-85)
Route::get('view/quote/{token}', [QuoteController::class, 'publicView']);
Route::get('view/invoice/{token}', [InvoiceController::class, 'publicView']);
```

> C'est **voulu** (partage client). Le token de 32 chars est acceptable.
> ⚠️ Cependant, il n'y a **pas de mécanisme d'expiration ni de révocation**.
> Envisager d'ajouter une date d'expiration ou un flag `token_active`.

---

### 10. `updateProfile` — `full_name` validé mais jamais sauvegardé

```php
$request->validate([
    'display_name' => 'required',
    'full_name'    => 'required',   // ← validé (champ obligatoire)
    ...
]);

$data = [
    'name'     => $request->display_name,
    // ❌ 'full_name' n'est jamais inclus dans $data → jamais persisté
    ...
];
```

> Le champ est validé (l'utilisateur voit une erreur s'il le laisse vide) mais la valeur n'est
> **jamais persistée en base**. Bug silencieux.

---

### 11. `QuoteController` / `InvoiceController` — Race condition sur la numérotation

```php
$nextNumber = 'DEV-' . date('Y') . '-' . str_pad(Quote::count() + 1, 4, '0', STR_PAD_LEFT);
```

> Si deux utilisateurs créent un devis simultanément, `Quote::count()` retournera la même valeur
> → **deux devis avec le même numéro**. La contrainte `unique:quotes` empêchera l'insertion mais
> l'UX sera mauvaise (erreur 422 inattendue).
>
> **Solution** : séquence dédiée en base, ou `SELECT ... FOR UPDATE`, ou un compteur atomique
> via `DB::table('quotes')->lockForUpdate()->count()`.
>
> **Même problème dans `InvoiceController`.**

---

## 🔵 Suggestions d'amélioration (non-bloquantes)

| # | Fichier | Suggestion |
|---|---------|------------|
| A | `ShopController` | Extraire le calcul du total du panier en méthode privée `getCartTotal()` — la logique est répétée 4 fois |
| B | `ProductController` | Créer un Form Request dédié `StoreProductRequest` — la validation est dupliquée entre `store()` et `update()` |
| C | `QuoteController` / `InvoiceController` | La logique "save to catalog" est dupliquée à l'identique dans 4 méthodes — extraire en méthode privée `saveToCatalog(array $item)` |
| D | `Order` model | Pas de relation inverse `user()` — utile pour eager loading depuis les commandes |
| E | `routes/web.php` | Les routes du dashboard utilisateur utilisent des FQCN inline (`\App\Http\...`) alors que les autres contrôleurs sont importés via `use`. Homogénéiser |
| F | `User` model | `photoUrl` est un `Attribute` cast mais n'est pas déclaré dans `$appends` — il ne sera pas sérialisé automatiquement en JSON |
| G | Général | Aucun test unitaire ou fonctionnel dans `/tests/` — ajouter au moins des tests sur `placeOrder` et la conversion devis→facture |

---

## Résumé des priorités

| Priorité | # | Description |
|----------|---|-------------|
| 🔴 Critique | 2 | Données non-validées utilisées dans `Quote`/`Invoice` (`$request` au lieu de `$validated`) |
| 🔴 Critique | 3 | Facture auto-marquée `paid` à la conversion d'un devis |
| 🔴 Critique | 4 | Absence de transaction DB sur `convert()` |
| 🔴 Critique | 10 | `full_name` validé mais jamais sauvegardé dans `updateProfile()` |
| 🟠 Important | 1 | Incohérence Route Model Binding sur `thanks()` |
| 🟠 Important | 7 | Slug toujours recalculé à la mise à jour du produit |
| 🟠 Important | 8 | `AdminUserSeeder` non appelé dans `DatabaseSeeder` |
| 🟠 Important | 11 | Race condition sur la numérotation des devis/factures |
| 🟡 Mineur | 5 | Doublon `'processing'` dans le `whereIn` des commandes en attente |
| 🟡 Mineur | 6 | `Session::get('cart')` sans fallback `[]` dans `removeFromCart()` |
| 🔵 Suggestion | A–G | Voir tableau ci-dessus |
