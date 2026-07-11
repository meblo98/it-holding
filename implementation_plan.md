# Audit & Plan d'Implémentation — IT HOLDING SERVICES ERP

## État des lieux : Ce qui est déjà en place ✅

| Module | Statut | Détails |
|--------|--------|---------|
| **Boutique e-commerce** | ✅ Fait | Catalogue, panier, checkout, commandes |
| **Gestion produits** | ✅ Fait | CRUD, photos, catégories, marques, prix achat/vente, stock, wholesale |
| **Gestion devis** | ✅ Fait | CRUD, impression, partage, conversion en facture |
| **Gestion factures** | ✅ Fait | CRUD, modes de paiement, impression, lien public |
| **Gestion commandes** | ✅ Partiel | Index + show, mise à jour statut — pas de cycle complet |
| **Bons de livraison** | ✅ Fait | Envoi client + Réception fournisseur, statuts, stepper, BL auto depuis commande/facture |
| **Gestion stock** | ✅ Fait | Journal mouvements, ajustements, alertes rupture, valorisation |
| **Gestion fournisseurs** | ✅ Fait | CRUD, fiche profil, KPIs, lien BL |
| **Blog / Portfolio / Services** | ✅ Fait | CRUD complet |
| **Dashboard** | ✅ Partiel | Graphiques CA, commandes — manquent les KPIs financiers/techniques |
| **Authentification** | ✅ Partiel | `is_admin` binaire uniquement — pas de rôles multiples |

---

## Ce qui RESTE à implémenter 🔨

> [!IMPORTANT]
> Les modules ci-dessous sont **prioritisés par impact métier immédiat**.
> Je propose de les implémenter par phases pour livrer rapidement de la valeur.

---

## PHASE 1 — Fondations critiques (Priorité haute)

### 1.1 Système de Rôles Utilisateurs
**Réf. CDC Partie 2**

Le système actuel n'a qu'un flag `is_admin` booléen. Il faut des rôles granulaires.

**À créer :**
- Migration : ajouter colonne `role` à `users` (admin, dg, commercial, comptable, magasinier, technicien, livreur)
- Middleware de contrôle d'accès par rôle
- Interface de gestion des utilisateurs internes (`/admin/users`)
- Restrictions d'accès dans le menu sidebar selon le rôle

---

### 1.2 Gestion Clients Professionnels (CRM)
**Réf. CDC Partie 3 & 11**

Actuellement les clients sont anonymes (juste les champs dans les commandes). Il faut une vraie base clients.

**À créer :**
- Modèle & migration `Client` (nom, prénom, entreprise, RCCM, NINEA, secteur…)
- CRUD `/admin/clients`
- Fiche client avec historique complet (commandes, devis, factures, paiements, garanties)
- Compte professionnel : crédit autorisé, solde, mode de règlement (semaine / 15j / mois / trimestre)
- Liaison des commandes/factures existantes avec `client_id`

---

### 1.3 Gestion des Garanties
**Réf. CDC Parties 13**

**À créer :**
- Modèle `Warranty` (produit, client, N° série, date achat, date expiration, type, statut)
- Création automatique à la validation d'une commande/facture
- CRUD `/admin/warranties`
- Alertes garanties expirantes dans le dashboard
- Espace client : voir ses garanties, vérifier un N° de série

---

## PHASE 2 — Modules SAV & Technique (Priorité haute)

### 2.1 Gestion SAV / Tickets
**Réf. CDC Partie 14**

**À créer :**
- Modèle `Ticket` (client, produit, garantie liée, description panne, photo, statut, priorité)
- CRUD `/admin/tickets`
- Workflow : Ouvert → Diagnostiqué → En cours → Résolu → Fermé
- Interface technicien : diagnostic, rapport, pièces utilisées
- Espace client : déclarer une panne, suivre sa réparation

---

### 2.2 Contrats de Maintenance
**Réf. CDC Partie 16**

**À créer :**
- Modèle `MaintenanceContract` (client, durée, prix, nb interventions, SLA, date début/fin, statut)
- CRUD `/admin/contracts`
- Alerte renouvellement automatique
- Lien avec les tickets SAV (interventions incluses dans le contrat)

---

### 2.3 IT HOLDING CARE+ (Abonnements Garantie)
**Réf. CDC Partie 13 (§18)**

**À créer :**
- Modèle `CareSubscription` (client, produit couvert, niveau, date début/fin, statut)
- Page d'abonnement frontend
- Avantages : réduction réparation, priorité, assistance

---

## PHASE 3 — Finance & Documents (Priorité moyenne)

### 3.1 Documents Commerciaux Manquants
**Réf. CDC Partie 8**

| Document | Statut |
|----------|--------|
| Devis | ✅ Fait |
| Facture Proforma | ❌ Manquant |
| Bon de Commande (BC) | ❌ Manquant |
| Bon de Livraison (BL) | ✅ Fait |
| Bon de Réception (BR) | ✅ (=Réception BL) |
| Facture définitive | ✅ Fait |
| Reçu de paiement | ❌ Manquant |
| Avoir / Note de crédit | ❌ Manquant |

**À créer :** Facture Proforma (clone de devis avec entête "PROFORMA"), Reçu de paiement auto, Avoir.

---

### 3.2 Livraison avec Paiement Différé
**Réf. CDC Partie 10 & 13**

**À créer :**
- Champ `credit_limit` + `payment_terms` sur la fiche Client professionnel
- Gestion des créances : montant livré, paiements reçus, solde, échéance
- Tableau des créances dans le dashboard comptable

---

### 3.3 Gestion Bancaire & Finance
**Réf. CDC Partie 17**

**À créer :**
- Modèle `BankAccount` (nom banque, IBAN, solde)
- Modèle `BankTransaction` (compte, type, montant, référence, date)
- Rapprochement bancaire basique
- Vue `/admin/finance`

---

### 3.4 Portefeuille Client (Wallet)
**Réf. CDC Partie 12**

**À créer :**
- Champ `wallet_balance` sur le modèle `Client`
- Historique dépôts/retraits/utilisations
- Paiement via wallet au checkout

---

## PHASE 4 — Fonctionnalités Avancées (Priorité normale)

### 4.1 Configuration Produits sur Mesure
**Réf. CDC Partie 6 & 7**

**À créer :**
- Modèle `ProductOption` (nom option, valeurs possibles, prix supplémentaire)
- Interface de configuration produit côté client
- Calcul automatique du prix selon options
- Demande de devis personnalisé

---

### 4.2 Rapports Automatiques
**Réf. CDC Partie 18**

**À créer :**
- Rapport ventes (par période, par produit, par client)
- Rapport stocks (valorisation, rotations, ruptures)
- Rapport bénéfices (CA - achats)
- Rapport fournisseurs
- Rapport garanties / SAV
- Export PDF et Excel

---

### 4.3 Portail Client (Espace Client)
**Réf. CDC Partie 19**

**À étendre :**
- L'utilisateur connecté voit : ses commandes, factures téléchargeables, garanties, N° de série, demande SAV, solde wallet, contrats Care+

---

### 4.4 Amélioration Dashboard KPIs
**Réf. CDC Partie 1**

**À ajouter :**
- KPIs financiers : factures payées/impayées, créances, trésorerie
- KPIs techniques : garanties actives/expirantes, tickets SAV ouverts
- Section "Top produits vendus" et "Services les plus demandés"

---

## PHASE 5 — Long terme

### 5.1 Application Mobile
**Réf. CDC Partie 20**
- API REST Laravel (Sanctum)
- App React Native ou Flutter (Client, Technicien, Commercial)

### 5.2 Assistant IA
**Réf. CDC Partie 21**
- Intégration OpenAI / Gemini API
- Conseiller produit, créer devis, relancer clients, alertes garantie

---

## Proposition d'ordre d'exécution

```
Phase 1 → Phase 2 → Phase 3 → Phase 4 → Phase 5
  ↓           ↓          ↓          ↓
Rôles      SAV        Finance    Config     Mobile
Clients    Contrats   Wallet     Rapports   IA
Garanties  Care+      Documents
```

---

> [!NOTE]
> **Par où commencer ?**
> Je recommande de démarrer par **Phase 1.2 (Clients CRM)** car c'est le pivot de tout : garanties, SAV, contrats et finance en dépendent. Ensuite **Phase 1.3 (Garanties)** et **Phase 2.1 (SAV)** qui sont très demandées en contexte IT.
>
> Dites-moi quelle phase ou quel module spécifique vous voulez commencer, et j'exécute immédiatement.
