# Yabiso Cloud — site d'outils en ligne en PHP, prêt pour Google AdSense

Site de **20 outils gratuits** répartis en 8 catégories, avec recherche instantanée sur la page d'accueil :

| Catégorie | Outils |
|---|---|
| Calcul | Pourcentage, remise et soldes, moyenne avec coefficients |
| Finance | TVA (HT ↔ TTC), simulateur de prêt, salaire brut ↔ net |
| Texte | Compteur de mots, majuscules/minuscules, Lorem Ipsum |
| Conversion | Unités, nombre en lettres (chèques) |
| Date & temps | Âge, jours entre deux dates (jours ouvrés, fériés), chronomètre/minuteur, horloge mondiale |
| Web & développeur | QR code (lien, Wi-Fi, e-mail…), couleurs HEX/RVB/HSL, Base64 |
| Sécurité | Générateur de mot de passe |
| Santé | IMC |

Avec toutes les pages exigées par AdSense : **À propos, Contact, Politique de confidentialité, Mentions légales, CGU**.

- PHP 8.0+ seulement, **aucune base de données**, aucune dépendance.
- Chaque outil a un vrai texte explicatif (formules, exemples, FAQ) : c'est ce qui évite le refus
  « contenu à faible valeur » d'AdSense.
- Design moderne et responsive, mode sombre automatique, barre latérale (emplacement publicitaire), sitemap et données structurées automatiques.
- Aucune ressource externe (police, script) hormis AdSense : pages rapides et conformes RGPD.

## Installation (≈ 15 minutes)

1. **Hébergement** : n'importe quel hébergement mutualisé PHP (o2switch, OVH, Hostinger, IONOS…) avec
   **un nom de domaine à vous** (pas de sous-domaine gratuit, AdSense les refuse) et **le HTTPS activé**.
2. **Modifier `config.php`** : nom du site, `site_url`, vos nom/adresse/e-mail, infos de l'hébergeur.
3. **Modifier `robots.txt`** : remplacez `https://www.exemple.fr` par votre adresse.
4. **Envoyer tous les fichiers** à la racine du site (dossier `www` ou `public_html`) par FTP (FileZilla)
   ou via le gestionnaire de fichiers de l'hébergeur.
5. Ouvrez votre site et testez chaque outil. Dans `.htaccess`, décommentez les 2 lignes « Forcer le HTTPS ».

> Installation dans un sous-dossier (ex. `mon-site.fr/outils`) : indiquez ce chemin dans `site_url` et adaptez la ligne
> `ErrorDocument 404` du `.htaccess`.

## Mise en ligne automatique par FTP (GitHub Actions)

Le fichier `.github/workflows/deploy-ftp.yml` envoie le site sur l'hébergement à chaque modification de la branche `main`.

1. Dans l'espace client de l'hébergeur, notez : **serveur FTP**, **identifiant**, **mot de passe**.
2. Sur GitHub : **Settings → Secrets and variables → Actions → New repository secret**, créez :
   `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` (et `FTP_SERVER_DIR` seulement si votre compte FTP ne s'ouvre pas directement dans le dossier du site : par ex. `public_html/` ou `www/`).
3. Fusionnez la branche dans `main` : l'envoi démarre tout seul (onglet **Actions**).
   Vous pouvez aussi le relancer à la main : **Actions → Déploiement FTP → Run workflow**.

## Demander la monétisation AdSense

1. Inscrivez le site sur **Google Search Console** et soumettez `https://votre-site/sitemap.xml`
   (ou `sitemap.php` si votre hébergeur ne gère pas la réécriture d'URL).
2. Créez votre compte sur **adsense.google.com** et ajoutez votre site.
3. Copiez votre identifiant éditeur (`ca-pub-…`) dans `config.php` → `adsense_client`.
   Le code de validation AdSense est alors automatiquement placé dans toutes les pages.
4. Dans `ads.txt`, remplacez `pub-0000000000000000` par votre identifiant (sans le `ca-`).
5. Dans AdSense → **Confidentialité et messages**, activez le **message de consentement RGPD** de Google
   (obligatoire pour les visiteurs européens, rien à coder).
6. Cliquez sur « Demander un examen ». Une fois accepté, activez les **annonces automatiques** :
   rien d'autre à faire. (Facultatif : créez des blocs manuels et collez leurs identifiants dans `adsense_slots` : haut, milieu, bas de page et barre latérale.)

## Soyons honnêtes sur le délai

Aucun site n'est accepté « garanti en 1 jour » : **c'est Google qui fixe le délai d'examen**
(de quelques jours à 2-4 semaines). Ce site s'installe en 1 jour et réunit les critères connus, mais
pour maximiser vos chances :

- ✅ Domaine propre + HTTPS, pages légales complètes (déjà fait), navigation claire (déjà fait).
- ✅ **Personnalisez** : changez le nom, réécrivez la page À propos avec votre vraie histoire, ajoutez vos propres outils.
  Un site identique à d'autres peut être refusé pour « contenu dupliqué ».
- ✅ Attendez que Google ait **indexé** vos pages (Search Console) avant de demander l'examen.
- ✅ Ajoutez si possible quelques articles/guides originaux (ex. « Comment calculer la TVA sur une facture »)
  et un peu de trafic réel.
- ❌ Ne cliquez jamais sur vos propres annonces et n'achetez pas de faux trafic : bannissement définitif.

## Ajouter un outil

1. Ajoutez une entrée dans `tools()` dans `inc/functions.php` (slug, icône, catégorie, titre, description).
   Les catégories et leurs couleurs se trouvent juste au-dessus, dans `categories()`.
2. Copiez un outil existant vers `outils/<slug>.php` et adaptez le formulaire, le calcul et le texte.

Il apparaît automatiquement sur l'accueil, dans les « Autres outils » et dans le sitemap.

## Structure

```
config.php            ← le seul fichier PHP à modifier
index.php             accueil (liste des outils)
outils/*.php          un fichier par outil
a-propos.php contact.php confidentialite.php mentions-legales.php cgu.php 404.php
sitemap.php           sitemap XML automatique (/sitemap.xml via .htaccess)
robots.txt ads.txt .htaccess
inc/                  fonctions, en-tête, pied de page
assets/               CSS + JS
```

Test en local : `php -S localhost:8000`, puis ouvrez http://localhost:8000
