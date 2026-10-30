<?php
/**
 * CONFIGURATION DU SITE — le seul fichier PHP à modifier avant la mise en ligne.
 */
return [
    // Nom affiché partout sur le site
    'site_name'     => 'Yabiso Cloud',
    'tagline'       => 'Outils en ligne gratuits, simples et rapides',

    // Adresse complète du site, SANS slash final (ex. https://www.mon-site.fr ou https://mon-site.fr/outils)
    'site_url'      => 'https://yabiso.cloud',

    // Informations légales (obligatoires pour les mentions légales et pour AdSense)
    'owner_name'    => 'Henry Fiti',
    'owner_address' => 'Avenue Bateke 6D, Kinshasa, République démocratique du Congo',
    'contact_email' => 'henryfiti1@gmail.com',
    'host_info'     => 'Hostinger International Ltd — 61 Lordou Vironos Street, 6023 Larnaca, Chypre — www.hostinger.com',

    // Google AdSense : votre identifiant éditeur, ex. 'ca-pub-1234567890123456'.
    // Laissez vide tant que vous n'avez pas de compte : aucune publicité ne sera affichée.
    'adsense_client' => 'ca-pub-5597349965916962',

    // (Facultatif) identifiants de blocs d'annonces manuels. Laissez vide pour n'utiliser que les « annonces automatiques ».
    'adsense_slots' => [
        'top'        => '',
        'in_content' => '',
        'sidebar'    => '',
        'bottom'     => '',
    ],
];
