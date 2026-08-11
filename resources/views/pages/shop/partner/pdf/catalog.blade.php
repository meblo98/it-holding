<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $catalogTitle }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header .logo {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .partner-info {
            text-align: right;
            font-size: 10px;
            color: #4b5563;
        }
        .partner-badge {
            background-color: #0f172a;
            color: #f59e0b;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            display: inline-block;
            margin-bottom: 4px;
        }
        h1 {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0 0 10px 0;
            font-style: italic;
        }
        .intro-text {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 20px;
            font-style: italic;
        }
        .product-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ffffff;
            page-break-inside: avoid;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
        }
        .product-image-td {
            width: 100px;
            vertical-align: top;
        }
        .product-image {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border: 1px solid #f3f4f6;
            border-radius: 5px;
            background-color: #f9fafb;
        }
        .product-details-td {
            padding-left: 15px;
            vertical-align: top;
        }
        .product-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .product-meta {
            font-size: 9px;
            color: #f59e0b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .product-description {
            font-size: 10px;
            color: #4b5563;
            margin-bottom: 10px;
        }
        .price-row {
            margin-top: 10px;
        }
        .product-price {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .buy-button {
            background-color: #f59e0b;
            color: #0f172a;
            text-decoration: none;
            padding: 5px 12px;
            font-weight: bold;
            font-size: 9px;
            border-radius: 4px;
            text-transform: uppercase;
            float: right;
            margin-top: -3px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    IT HOLDING
                </td>
                <td class="partner-info">
                    <span class="partner-badge">Partenaire Officiel</span><br>
                    <strong>{{ $partner->name }}</strong><br>
                    Téléphone : {{ $partner->phone ?: 'N/A' }}<br>
                    Email : {{ $partner->email }}
                </td>
            </tr>
        </table>
    </div>

    <h1>{{ $catalogTitle }}</h1>
    <p class="intro-text">
        Ce catalogue a été préparé spécialement pour vous par notre partenaire agréé <strong>{{ $partner->name }}</strong>. 
        En commandant avec le code promo ci-dessous, vous bénéficiez de conditions exclusives et de tarifs préférentiels.
    </p>

    @if($partner->partner_code)
    <div style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 10px; margin-bottom: 25px; text-align: center;">
        <span style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #b45309; display: block; margin-bottom: 2px;">Votre code de réduction partenaire (-5%)</span>
        <strong style="font-size: 16px; color: #78350f; letter-spacing: 1px;">{{ $partner->partner_code }}</strong>
    </div>
    @endif

    <div class="products-list">
        @foreach($products as $product)
            <div class="product-card">
                <table class="product-table">
                    <tr>
                        <td class="product-image-td">
                            @if($product->image)
                                <img src="{{ public_path('storage/' . $product->image) }}" class="product-image">
                            @else
                                <div style="width: 90px; height: 90px; border: 1px solid #f3f4f6; border-radius: 5px; background-color: #f9fafb; text-align: center; line-height: 90px; color: #9ca3af; font-size: 24px;">📦</div>
                            @endif
                        </td>
                        <td class="product-details-td">
                            <h2 class="product-name">{{ $product->name }}</h2>
                            <div class="product-meta">
                                État : {{ $product->condition === 'new' ? 'Neuf' : ($product->condition === 'refurbished' ? 'Reconditionné' : 'Occasion') }} 
                                @if($product->stock > 0)
                                    | En stock
                                @else
                                    | Sur commande
                                @endif
                            </div>
                            <div class="product-description">
                                {{ $product->description ?: 'Aucune description disponible pour ce produit.' }}
                            </div>
                            <div class="price-row">
                                <span class="product-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                <a href="{{ route('shop.show', $product->slug) }}?ref={{ $partner->partner_code }}" class="buy-button" target="_blank">Commander ➜</a>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

    <div class="footer">
        IT HOLDING S.N. - Catalogue généré via l'Espace Partenaire. Tous droits réservés.
    </div>

</body>
</html>
