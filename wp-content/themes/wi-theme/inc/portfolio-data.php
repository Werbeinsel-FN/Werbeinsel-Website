<?php
/**
 * Portfolio project data – 6 projekata kao u dizajnu (Desktop/sajt)
 * Koristi se u portfolio-detail-template.php
 */
if (!function_exists('wi_get_portfolio_projects')) {
  function wi_get_portfolio_projects() {
    return [
      1 => [
        'id' => 1,
        'title' => 'Fahrzeugbeschriftung Premium',
        'category' => 'BESCHRIFTUNG',
        'client' => 'Burger Brothers GmbH',
        'year' => '2024',
        'location' => 'München',
        'heroImage' => 'https://images.unsplash.com/photo-1664314383485-1070ad9dddbc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Burger Brothers benötigte eine auffällige Fahrzeugflotte, die als mobile Werbefläche fungiert und gleichzeitig die Markenidentität stärkt. Die Herausforderung: Ein Design, das auf verschiedenen Fahrzeugtypen funktioniert.',
        'solution' => 'Vollverklebung der gesamten Fahrzeugflotte mit mutigem Branding im Corporate Design. Einsatz hochwertiger 3M-Folie für Langlebigkeit und UV-Beständigkeit. Das Design integriert QR-Codes für direkten Social-Media-Link.',
        'results' => [
          ['label' => 'Fahrzeuge', 'value' => '12'],
          ['label' => 'Reichweite/Tag', 'value' => '50.000+'],
          ['label' => 'ROI Steigerung', 'value' => '340%'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1664314383485-1070ad9dddbc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1565123409695-7b5ef63a2efb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['Vollverklebung', 'Design-Konzept', 'Montage vor Ort'],
      ],
      2 => [
        'id' => 2,
        'title' => 'Schaufenster-Werbung Premium Store',
        'category' => 'BESCHRIFTUNG',
        'client' => 'Mode Boutique Premium',
        'year' => '2024',
        'location' => 'Berlin Mitte',
        'heroImage' => 'https://images.unsplash.com/photo-1543949144-a8b18c58a635?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Die Boutique musste sich in einer belebten Einkaufsstraße von der Konkurrenz abheben. Klassische Schaufenster-Werbung wirkte veraltet und zog nicht genug Aufmerksamkeit.',
        'solution' => 'Großformatige Window Graphics mit saisonalen Motiven, kombiniert mit LED-Leuchtbuchstaben. Einsatz von Milchglasfolie für Privacy und gleichzeitig als Werbefläche. Wechselbare Elemente für flexible Kampagnen.',
        'results' => [
          ['label' => 'Laufkundschaft', 'value' => '+180%'],
          ['label' => 'Social Shares', 'value' => '2.400+'],
          ['label' => 'Umsatzsteigerung', 'value' => '+65%'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1543949144-a8b18c58a635?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1441986300917-64674bd600d8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1555421689-d68471e189f2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['Schaufenstergestaltung', 'LED-Leuchtbuchstaben', 'Folierung'],
      ],
      3 => [
        'id' => 3,
        'title' => 'Großflächenplakat Kampagne',
        'category' => 'PLAKATIERUNG',
        'client' => 'Stadtwerke München',
        'year' => '2024',
        'location' => 'München City',
        'heroImage' => 'https://images.unsplash.com/photo-1769578911474-af7aae3f84e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Launch einer nachhaltigen Energie-Initiative mit dem Ziel, 50.000 Haushalte zu erreichen. Die Kampagne musste binnen 4 Wochen maximale Sichtbarkeit erzielen.',
        'solution' => '18/1-Großflächen an Premium-Standorten mit hoher Frequenz. Strategische Platzierung an Hauptverkehrsachsen und U-Bahn-Stationen. Kombination aus klassischen Plakaten und beleuchteten City-Light-Postern.',
        'results' => [
          ['label' => 'Plakate', 'value' => '45'],
          ['label' => 'Impressions', 'value' => '2.8 Mio.'],
          ['label' => 'Kampagnendauer', 'value' => '8 Wochen'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1769578911474-af7aae3f84e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1551135049-82b8b3f13c7e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['18/1-Großflächen', 'City-Light-Poster', 'Standortanalyse'],
      ],
      4 => [
        'id' => 4,
        'title' => 'Leuchtreklame Premium',
        'category' => 'AUSSENWERBUNG',
        'client' => 'Restaurant "Zur Perle"',
        'year' => '2024',
        'location' => 'Hamburg Reeperbahn',
        'heroImage' => 'https://images.unsplash.com/photo-1568154700421-490b598b1a47?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Ein Restaurant in Hamburgs Ausgehviertel musste sich visuell gegen 200+ Konkurrenten durchsetzen. Die alte Beschilderung war kaum sichtbar und nicht instagrammable.',
        'solution' => 'Maßgeschneiderte LED-Neon-Reklame im Vintage-Stil mit modernem LED-Kern. Wetterfeste Konstruktion mit RGB-Steuerung für verschiedene Lichtszenarien. Installation einer zusätzlichen Fenster-Leuchtschrift.',
        'results' => [
          ['label' => 'Sichtbarkeit', 'value' => '+500m'],
          ['label' => 'Instagram Posts', 'value' => '8.500+'],
          ['label' => 'Reservierungen', 'value' => '+220%'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1568154700421-490b598b1a47?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1513407030348-c983a97b98d8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1576522564402-4a3936854451?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['LED-Neon-Technik', 'Lichtplanung', 'Montage & Wartung'],
      ],
      5 => [
        'id' => 5,
        'title' => 'City-Light-Poster Kampagne',
        'category' => 'PLAKATIERUNG',
        'client' => 'FitLife Gym',
        'year' => '2024',
        'location' => 'Frankfurt am Main',
        'heroImage' => 'https://images.unsplash.com/photo-1640146459297-eb4f1b084cc6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Neukundengewinnung für Premium-Fitnessstudio im hart umkämpften Frankfurter Markt. Zielgruppe: Young Professionals, 25-40 Jahre.',
        'solution' => '60 City-Light-Poster an Bushaltestellen und U-Bahn-Stationen im Geschäftsviertel. Beleuchtete Vitrinen für 24/7 Sichtbarkeit. QR-Code-Integration für direkten Probetraining-Buchungslink.',
        'results' => [
          ['label' => 'Standorte', 'value' => '60'],
          ['label' => 'Neumitglieder', 'value' => '340'],
          ['label' => 'Conversion Rate', 'value' => '12.8%'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1640146459297-eb4f1b084cc6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['City-Light-Poster', 'Standort-Targeting', 'Performance-Tracking'],
      ],
      6 => [
        'id' => 6,
        'title' => 'Fassaden-Beschriftung Luxus',
        'category' => 'BESCHRIFTUNG',
        'client' => 'Juwelier Goldstein',
        'year' => '2024',
        'location' => 'Düsseldorf Königsallee',
        'heroImage' => 'https://images.unsplash.com/photo-1758862495985-ab1cd2f7d613?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        'challenge' => 'Luxus-Juwelier in Premiumlage benötigte eine Fassadenbeschriftung, die Exklusivität ausstrahlt und gleichzeitig denkmalgeschützte Auflagen erfüllt.',
        'solution' => 'Vergoldete 3D-Buchstaben mit LED-Hintergrundbeleuchtung. Hochwertige Edelstahlkonstruktion mit matter Goldveredelung. Indirekte Beleuchtung für eleganten Abend-Look ohne grelle Lichter.',
        'results' => [
          ['label' => 'Buchstabenhöhe', 'value' => '180 cm'],
          ['label' => 'Prestige-Effekt', 'value' => '+400%'],
          ['label' => 'Kundenfrequenz', 'value' => '+95%'],
        ],
        'images' => [
          'https://images.unsplash.com/photo-1758862495985-ab1cd2f7d613?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
          'https://images.unsplash.com/photo-1580674684081-7617fbf3d745?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ],
        'services' => ['3D-Leuchtbuchstaben', 'Gold-Veredelung', 'Denkmalschutz-Planung'],
      ],
    ];
  }
}
