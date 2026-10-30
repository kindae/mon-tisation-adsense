<?php
declare(strict_types=1);

define('APP', true);
mb_internal_encoding('UTF-8');

$GLOBALS['config'] = require __DIR__ . '/../config.php';

function cfg(string $key, $default = null)
{
    return $GLOBALS['config'][$key] ?? $default;
}

/** Échappe une valeur pour l'affichage HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Lien relatif à la racine du site (fonctionne aussi dans un sous-dossier). */
function url(string $path = ''): string
{
    $base = rtrim((string) parse_url((string) cfg('site_url'), PHP_URL_PATH), '/');
    return $base . '/' . ltrim($path, '/');
}

/** URL absolue (balise canonical, sitemap). */
function abs_url(string $path = ''): string
{
    return rtrim((string) cfg('site_url'), '/') . '/' . ltrim($path, '/');
}

/** Catégories : nom => [couleur, icône]. */
function categories(): array
{
    return [
        'Calcul'           => ['#2563eb', '🧮'],
        'Finance'          => ['#059669', '💶'],
        'Texte'            => ['#7c3aed', '📝'],
        'Conversion'       => ['#4f46e5', '🔁'],
        'Date & temps'     => ['#d97706', '⏱️'],
        'Web & développeur'=> ['#0891b2', '💻'],
        'Sécurité'         => ['#dc2626', '🛡️'],
        'Santé'            => ['#db2777', '❤️'],
    ];
}

function cat_slug(string $cat): string
{
    return trim(preg_replace('/[^a-z]+/', '-', strtolower(strtr($cat, ['é' => 'e', '&' => '']))), '-');
}

function cat_color(string $cat): string
{
    return categories()[$cat][0] ?? '#2563eb';
}

/** Liste des outils : ajoutez une entrée ici + un fichier outils/<slug>.php pour créer un nouvel outil. */
function tools(): array
{
    return [
        // Calcul
        'calcul-pourcentage' => ['icon' => '％', 'cat' => 'Calcul', 'title' => 'Calculateur de pourcentage',
            'desc' => 'Calculez un pourcentage, une proportion ou un taux d\'évolution entre deux valeurs.'],
        'calcul-remise' => ['icon' => '🏷️', 'cat' => 'Calcul', 'title' => 'Calcul de remise et de soldes',
            'desc' => 'Trouvez le prix après réduction, le montant économisé ou le pourcentage de remise appliqué.'],
        'calcul-moyenne' => ['icon' => '🎓', 'cat' => 'Calcul', 'title' => 'Calculateur de moyenne avec coefficients',
            'desc' => 'Calculez une moyenne simple ou pondérée de vos notes, sur 20 ou sur le barème de votre choix.'],
        // Finance
        'calcul-tva' => ['icon' => '🧾', 'cat' => 'Finance', 'title' => 'Calcul de TVA (HT ↔ TTC)',
            'desc' => 'Convertissez un montant hors taxes en toutes taxes comprises et inversement.'],
        'calcul-pret' => ['icon' => '🏦', 'cat' => 'Finance', 'title' => 'Simulateur de prêt',
            'desc' => 'Calculez la mensualité, le coût total et le tableau d\'amortissement d\'un crédit.'],
        'salaire-brut-net' => ['icon' => '💼', 'cat' => 'Finance', 'title' => 'Convertisseur salaire brut / net',
            'desc' => 'Estimez votre salaire net à partir du brut (et inversement), mensuel, annuel et horaire.'],
        // Texte
        'compteur-de-mots' => ['icon' => '📝', 'cat' => 'Texte', 'title' => 'Compteur de mots et de caractères',
            'desc' => 'Comptez les mots, caractères, phrases et paragraphes d\'un texte et estimez son temps de lecture.'],
        'convertisseur-casse' => ['icon' => '🔠', 'cat' => 'Texte', 'title' => 'Convertisseur majuscules / minuscules',
            'desc' => 'Passez un texte en majuscules, minuscules, casse de titre ou de phrase, ou retirez les accents.'],
        'lorem-ipsum' => ['icon' => '📄', 'cat' => 'Texte', 'title' => 'Générateur de Lorem Ipsum',
            'desc' => 'Générez du faux texte en paragraphes, phrases ou mots pour vos maquettes et mises en page.'],
        // Conversion
        'convertisseur-unites' => ['icon' => '📏', 'cat' => 'Conversion', 'title' => 'Convertisseur d\'unités',
            'desc' => 'Convertissez longueurs, masses, températures et volumes en un clic.'],
        'nombre-en-lettres' => ['icon' => '✍️', 'cat' => 'Conversion', 'title' => 'Écrire un nombre en lettres',
            'desc' => 'Convertissez un nombre ou un montant en toutes lettres, pour un chèque ou une facture.'],
        'convertisseur-couleurs' => ['icon' => '🎨', 'cat' => 'Web & développeur', 'title' => 'Convertisseur de couleurs HEX, RVB, HSL',
            'desc' => 'Convertissez une couleur entre les formats HEX, RVB et HSL et générez ses nuances.'],
        // Date & temps
        'calcul-age' => ['icon' => '🎂', 'cat' => 'Date & temps', 'title' => 'Calculateur d\'âge',
            'desc' => 'Obtenez votre âge exact en années, mois et jours, et le nombre de jours avant votre anniversaire.'],
        'jours-entre-dates' => ['icon' => '📅', 'cat' => 'Date & temps', 'title' => 'Nombre de jours entre deux dates',
            'desc' => 'Comptez les jours, semaines et jours ouvrés entre deux dates, ou ajoutez des jours à une date.'],
        'chronometre-minuteur' => ['icon' => '⏱️', 'cat' => 'Date & temps', 'title' => 'Chronomètre et minuteur en ligne',
            'desc' => 'Un chronomètre avec tours et un compte à rebours avec alarme sonore, en plein écran.'],
        'horloge' => ['icon' => '🕒', 'cat' => 'Date & temps', 'title' => 'Horloge en ligne et heure mondiale',
            'desc' => 'Affichez l\'heure exacte en grand, au format 12 h ou 24 h, et l\'heure dans les grandes villes du monde.'],
        // Web & développeur
        'generateur-qr-code' => ['icon' => '🔳', 'cat' => 'Web & développeur', 'title' => 'Générateur de QR code',
            'desc' => 'Créez un QR code gratuit pour un lien, un texte, un Wi-Fi ou un e-mail, et téléchargez-le en image.'],
        'base64' => ['icon' => '🔐', 'cat' => 'Web & développeur', 'title' => 'Encodeur / décodeur Base64',
            'desc' => 'Encodez un texte en Base64 ou décodez une chaîne Base64 en texte lisible.'],
        // Sécurité
        'generateur-mot-de-passe' => ['icon' => '🔑', 'cat' => 'Sécurité', 'title' => 'Générateur de mot de passe',
            'desc' => 'Créez des mots de passe forts et aléatoires, avec la longueur et les caractères de votre choix.'],
        // Santé
        'calcul-imc' => ['icon' => '⚖️', 'cat' => 'Santé', 'title' => 'Calculateur d\'IMC',
            'desc' => 'Calculez votre indice de masse corporelle et situez-le dans la classification de l\'OMS.'],
    ];
}

function tool(string $slug): array
{
    $tools = tools();
    return ['slug' => $slug] + $tools[$slug];
}

function tool_url(string $slug): string
{
    return url('outils/' . $slug . '.php');
}

/** Lit un champ POST sous forme de chaîne. */
function post(string $key, string $default = ''): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : $default;
}

/** Lit un nombre saisi (accepte la virgule française). Retourne null si invalide. */
function post_num(string $key): ?float
{
    $raw = str_replace([' ', "\u{00A0}", ','], ['', '', '.'], post($key));
    return is_numeric($raw) ? (float) $raw : null;
}

/** Formate un nombre à la française, sans zéros inutiles. */
function fmt(float $n, int $decimals = 2): string
{
    $s = number_format($n, $decimals, ',', "\u{202F}");
    if ($decimals > 0) {
        $s = rtrim(rtrim($s, '0'), ',');
    }
    return $s === '-0' ? '0' : $s;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** Affiche un bloc d'annonce manuel si un identifiant est configuré (sinon rien). */
function ad(string $position): void
{
    $client = cfg('adsense_client');
    $slot = cfg('adsense_slots', [])[$position] ?? '';
    if (!$client || !$slot) {
        return;
    }
    echo '<div class="ad"><ins class="adsbygoogle" style="display:block" data-ad-client="' . e($client)
        . '" data-ad-slot="' . e($slot) . '" data-ad-format="auto" data-full-width-responsive="true"></ins>'
        . '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script></div>';
}
