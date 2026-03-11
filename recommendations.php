<?php
if (!isset($_COOKIE['SessionKey'])) {
    header('Location: login.html');
    exit();
}

// get recommendations from cookie if available
$recommendations = [];
if (isset($_COOKIE['Recommendations'])) {
    $recommendations = json_decode($_COOKIE['Recommendations'], true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivalists - Recommendations</title>
    <link rel="stylesheet" href="css/recommendations.css">
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <div class="profileUser">
                <img src="images/dashboardImage.jpg" alt="User">
                <h1>Your <strong>Recommendations</strong></h1>
        </div>
        <span class="status">Session Active</span>
    </div>

    <div class="content">
        <?php if (empty($recommendations)): ?>
            <div class="empty-state">
                <p>No recommendations yet! Leave some reviews first and we'll suggest music you'll love.</p>
                <a href="review.html" class="actionLink">Leave a Review &rarr;</a>
            </div>
        <?php else: ?>
            <p class="subtitle">Based on your reviews, we think you'll love these:</p>
            <div class="recommendations-grid">
                <?php foreach ($recommendations as $item): ?>
                <div class="rec-card">
                    <div class="rec-type"><?php echo htmlspecialchars($item['type'] ?? 'artist'); ?></div>
                    <div class="rec-title"><?php echo htmlspecialchars($item['name'] ?? 'unknown'); ?></div>
                    <div class="rec-popularity">Popularity: <?php echo round(($item['popularity'] ?? 0) * 100); ?>%</div>
                        <?php if (!empty($item['tidalUrl'])): ?>
                        <a href="<?php echo htmlspecialchars($item['tidalUrl']); ?>" 
                        target="_blank" class="tidal-link">Listen on Tidal &rarr;</a>
                        <?php endif; ?>
                    <!--
                        <div class="rec-genres">
                        <?php foreach ($item['genres'] as $genre): ?>
                            <span class="genre-tag"><?php echo htmlspecialchars($genre); ?></span>
                        <?php endforeach;?>
                    </div>
                        -->
        
                    <div class="rec-reason"><?php echo htmlspecialchars($item['reason'] ?? ''); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer-links">
        <a href="recommendationsRequest.php" class="actionLink">Refresh Recommendations &rarr;</a>
        <a href="dashboard.php" class="logoutButton">&larr; Back to Dashboard</a>
    </div>
</div>
</body>
</html>