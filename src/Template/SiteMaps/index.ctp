<?php
use Cake\Core\Configure;

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '  <url>';
echo '      <loc>'.$fullbaseUrl.'</loc>';
echo '      <changefreq>'.$changefreq.'</changefreq>';
echo '  </url>';
foreach ($results as $result) {
    echo '<url>';
    echo '    <loc>'.$fullbaseUrl.'/site/produttore/'.$result->slug.'</loc>';
    echo '    <changefreq>'.$changefreq.'</changefreq>';
    echo '</url>';
}
echo '</urlset>';