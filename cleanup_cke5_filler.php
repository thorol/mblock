<?php

/**
 * MBlock CKEditor 5 Filler Cleanup Script
 * 
 * Run this script in the REDAXO PHP Console (System > Console) or via CLI
 * to remove existing filler content from your database.
 * 
 * Usage:
 * - Copy content to REDAXO Console and execute
 * - OR run via CLI: php redaxo/bin/console mblock:cleanup-cke5
 */

$tables = [
    'rex_article_slice' => 20, // Check value1 - value20
    // Add custom YForm tables here if needed:
    // 'rex_my_table' => ['description', 'text_column'],
];

$filler1 = '<p><br data-cke-filler="true"></p>';
$filler2 = '<br data-cke-filler="true">';

// JSON escaped variants (often found in MBlock data)
$filler1_json = '<p><br data-cke-filler=\"true\"><\/p>';
$filler2_json = '<br data-cke-filler=\"true\">';

echo "<h2>Cleaning CKEditor 5 Filler Content...</h2>";

foreach ($tables as $table => $columns) {
    if (is_int($columns)) {
        // Generate value1...valueN for slices
        $cols = [];
        for ($i = 1; $i <= $columns; $i++) {
            $cols[] = 'value' . $i;
        }
    } else {
        $cols = (array) $columns;
    }

    $sql = rex_sql::factory();
    
    foreach ($cols as $col) {
        // 1. Standard HTML replacement
        $query = "UPDATE `$table` SET `$col` = REPLACE(`$col`, :filler1, '') WHERE `$col` LIKE :search1";
        $sql->setQuery($query, ['filler1' => $filler1, 'search1' => '%data-cke-filler%']);
        if ($sql->getRows() > 0) echo "Updated $table.$col (HTML Variant 1): " . $sql->getRows() . " rows<br>";

        $query = "UPDATE `$table` SET `$col` = REPLACE(`$col`, :filler2, '') WHERE `$col` LIKE :search2";
        $sql->setQuery($query, ['filler2' => $filler2, 'search2' => '%data-cke-filler%']);
        if ($sql->getRows() > 0) echo "Updated $table.$col (HTML Variant 2): " . $sql->getRows() . " rows<br>";

        // 2. JSON Escaped replacement (common in mblock)
        $query = "UPDATE `$table` SET `$col` = REPLACE(`$col`, :filler1, '') WHERE `$col` LIKE :search1";
        $sql->setQuery($query, ['filler1' => $filler1_json, 'search1' => '%data-cke-filler%']);
        if ($sql->getRows() > 0) echo "Updated $table.$col (JSON Variant 1): " . $sql->getRows() . " rows<br>";

        $query = "UPDATE `$table` SET `$col` = REPLACE(`$col`, :filler2, '') WHERE `$col` LIKE :search2";
        $sql->setQuery($query, ['filler2' => $filler2_json, 'search2' => '%data-cke-filler%']);
        if ($sql->getRows() > 0) echo "Updated $table.$col (JSON Variant 2): " . $sql->getRows() . " rows<br>";

        // 3. Regex Replacement for Placeholders (PHP-based because SQL lacks REGEX replace)
        // Matches: <p class="ck-placeholder" data-placeholder="..."></p>
        $fetchQuery = "SELECT id, `$col` FROM `$table` WHERE `$col` LIKE '%ck-placeholder%'";
        // Iterate only over rows that actually match
        $iterator = rex_sql::factory()->getArray($fetchQuery);
        $localUpdateCount = 0;

        foreach ($iterator as $row) {
            $original = $row[$col];
            $current = $original;

            // Regex for HTML variant
            $current = preg_replace('/<p class="ck-placeholder" data-placeholder="[^"]+"><\/p>/', '', $current);
            
            // Regex for JSON escaped variant (need to be careful with backslashes)
            // Pattern: <p class=\"ck-placeholder\" data-placeholder=\"...\"><\/p>
            $current = preg_replace('/<p class=\\\\"ck-placeholder\\\\" data-placeholder=\\\\"[^"]+\\\\"><\\\\\/p>/', '', $current);
            
            if ($current !== $original) {
                // Determine ID column name (usually 'id')
                $idCol = 'id'; 
                
                $upd = rex_sql::factory();
                $upd->setTable($table);
                $upd->setWhere([$idCol => $row[$idCol]]);
                $upd->setValue($col, $current);
                $upd->update();
                $localUpdateCount++;
            }
        }
        if ($localUpdateCount > 0) echo "Updated $table.$col (Placeholders via PHP Regex): " . $localUpdateCount . " rows<br>";
    }
}

echo "<br><strong>Cleanup complete!</strong> Clear the cache manually if needed.";
