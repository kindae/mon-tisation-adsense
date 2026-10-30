<?php
/**
 * CONFIGURATION DU SITE — le seul fichier PHP à modifier avant la mise en ligne.
 */
return [
    // Nom affiché partout sur le site
    'site_name'     => 'OutilsMalins',
    'tagline'       => 'Outils en ligne gratuits, simples et rapides',

    // Adresse complète du site, SANS slash final (ex. https://www.mon-site.fr ou https://mon-site.fr/outils)
    'site_url'      => 'https://www.exemple.fr',

    // Informations légales (obligatoires pour les mentions légales et pour AdSense)
    'owner_name'    => 'Prénom Nom',
    'owner_address' => '1 rue Exemple, 75000 Paris, France',
    'contact_email' => 'contact@exemple.fr',
    'host_info'     => 'Nom de l\'hébergeur — adresse de l\'hébergeur — téléphone',

    // Google AdSense : votre identifiant éditeur, ex. 'ca-pub-1234567890123456'.
    // Laissez vide tant que vous n'avez pas de compte : aucune publicité ne sera affichée.
    'adsense_client' => '',

    // (Facultatif) identifiants de blocs d'annonces manuels. Laissez vide pour n'utiliser que les « annonces automatiques ».
    'adsense_slots' => [
        'top'        => '',
        'in_content' => '',
        'bottom'     => '',
    ],
];
