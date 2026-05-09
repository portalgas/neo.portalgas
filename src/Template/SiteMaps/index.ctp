<?php
use Cake\Core\Configure;

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '  <url>';
echo '      <loc>'.$fullbaseUrl.'</loc>';
echo '      <changefreq>'.$changefreq.'</changefreq>';
echo '  </url>';
foreach ($suppliers as $supplier) {
    echo '<url>';
    echo '    <loc>'.$fullbaseUrl.'/site/produttore/'.$supplier->slug.'</loc>';
    echo '    <changefreq>'.$changefreq.'</changefreq>';
    echo '</url>';
}
foreach ($organizations as $organization) {
    echo '<url>';
    echo '    <loc>'.$fullbaseUrl.'/gas/'.$organization->j_seo.'/home</loc>';
    echo '    <loc>'.$fullbaseUrl.'/gas/'.$organization->j_seo.'/consegne</loc>';
    echo '    <changefreq>'.$changefreq.'</changefreq>';
    echo '</url>';
}
echo '</urlset>';