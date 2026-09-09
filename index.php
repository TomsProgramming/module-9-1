<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

use Carbon\Carbon;

// Databaseverbinding maken
$conn = new PDO(
    "mysql:host={$config['database']['host']};dbname={$config['database']['name']};charset=utf8mb4",
    $config['database']['user'],
    $config['database']['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$error = '';

// Formulierafhandeling als er een POST-verzoek is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $text  = trim($_POST['comment'] ?? '');

    // Invoer valideren op verplichte velden, lengte en formaat
    if ($name === '' || $email === '' || $text === '') {
        $error = 'Vul alle verplichte velden in.';
    } elseif (mb_strlen($name) > 50) {
        $error = 'De naam mag niet langer zijn dan 50 tekens.';
    } elseif (!preg_match("/^[\p{L}\s0-9\-']+$/u", $name)) {
        $error = 'De naam bevat ongeldige tekens.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vul een geldig e-mailadres in.';
    } elseif (mb_strlen($email) > 100) {
        $error = 'Het e-mailadres mag niet langer zijn dan 100 tekens.';
    } elseif (mb_strlen($text) > 500) {
        $error = 'Het commentaar mag niet langer zijn dan 500 tekens.';
    }

    // Gegevens veilig opslaan als er geen fouten zijn
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO comments (`name`, `email`, `text`) VALUES (:name, :email, :text)");
        $stmt->execute([
            ':name'  => $name,
            ':email' => $email,
            ':text'  => $text,
        ]);

        // Pagina herladen om dubbele inzendingen bij F5 te voorkomen
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Alle reacties ophalen uit de database (nieuwste eerst)
$getComments = $conn->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>9.1 challenge</title>
    <link rel="stylesheet" href="style.css">
</head>

<body id="top">
    <header class="site-header">
        <div class="header-content">
            <button id="menu-button" aria-label="Menu openen" aria-expanded="false" class="icon-button menu-button">
                <svg class="icon icon-menu" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="#" class="brand">
                <span class="brand-icon"><img class="youtube-logo" src="https://cdn.simpleicons.org/youtube/ffffff" alt="YouTube-logo"></span>
                <span class="brand-name">YouTube</span>
                <sup class="brand-country">NL</sup>
            </a>
            <form id="search-form" class="search-form">
                <input id="search-input" class="search-input" placeholder="Zoeken" type="search">
                <button aria-label="Zoeken" class="search-button">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" stroke-width="2" />
                        <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                    </svg>
                </button>
            </form>
            <button id="mobile-search-button" aria-label="Zoeken openen" class="icon-button mobile-search-button">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2" />
                    <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4" />
                </svg>
            </button>
        </div>
        <div id="mobile-menu" class="mobile-menu">
            <nav>
                <a href="#video">Video</a>
                <a href="#beschrijving">Beschrijving</a>
                <a href="#reacties">Reacties</a>
                <a href="#top">Naar boven</a>
            </nav>
        </div>
    </header>

    <main class="video-page">
        <video id="video" class="video-player" controls preload="metadata" poster="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80">
            <source src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4" type="video/mp4">
            Je browser ondersteunt geen video-element.
        </video>

        <h1 class="video-title">10 Programmer Stereotypes</h1>
        <section class="video-meta">
            <div class="channel-info">
                <div class="channel-avatar">🔥</div>
                <div>
                    <h2>Fireship</h2>
                    <p>2,48 mln. abonnees</p>
                </div>
                <button class="subscribe-button">Abonneren</button>
            </div>
            <div class="video-actions">
                <button>♡ 124K</button>
                <button>Delen</button>
                <button aria-label="Meer opties">•••</button>
            </div>
        </section>

        <section id="beschrijving" class="description">
            <p class="description-heading">2,6 mln. weergaven • 1 jaar geleden <span>#comedy #programming #tech</span></p>
            <p class="description-text">Programmers are weird. It’s human nature to put people into a box with stereotypes and the tech industry is no exception. Let’s take a look at 10 common stereotypes people use for software engineers and developers.</p>
            <button class="more-button">...meer</button>
        </section>

        <section id="reacties" class="comments">
            <div class="comments-header">
                <h2><?= count($getComments); ?> reacties</h2>
                <button class="sort-button"><span>☷</span> Sorteren op</button>
            </div>

            <!-- Foutmelding tonen als de validatie faalt -->
            <?php if (!empty($error)): ?>
                <p style="color: red; margin-bottom: 1rem;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form method="post" class="comment-form">
                <div class="comment-form-avatar">G</div>
                <div class="comment-form-content">
                    <div class="comment-fields">
                        <input name="name" required placeholder="Naam" type="text" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <input name="email" required placeholder="E-mail" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <input name="comment" required class="comment-input" placeholder="Reactie toevoegen..." type="text" value="<?= htmlspecialchars($_POST['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="comment-submit">
                        <button type="submit">Reageren</button>
                    </div>
                </div>
            </form>

            <div class="comment-list">
                <!-- Reacties doorlopen en tonen -->
                <?php foreach ($getComments as $comment): ?>
                    <article class="comment">
                        <div class="comment-avatar">
                            <?= htmlspecialchars(mb_substr($comment['name'], 0, 1), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="comment-content">
                            <div class="comment-heading">
                                <h3>@<?= htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <!-- Datum omzetten naar relatieve tijd (bijv. "2 uur geleden") -->
                                <span><?= Carbon::parse($comment['created_at'])->locale('nl')->diffForHumans(); ?></span>
                            </div>
                            <p><?= htmlspecialchars($comment['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="comment-actions">
                                <button>Beantwoorden</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>

</html>