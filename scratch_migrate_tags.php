<?php
try {
    $db = new PDO('sqlite:database/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column already exists for posts
    $postCols = $db->query("PRAGMA table_info(posts)")->fetchAll(PDO::FETCH_ASSOC);
    $postTagsExists = false;
    foreach ($postCols as $col) {
        if ($col['name'] === 'tags') {
            $postTagsExists = true;
            break;
        }
    }
    
    if (!$postTagsExists) {
        $db->exec("ALTER TABLE posts ADD COLUMN tags TEXT");
        echo "Added 'tags' column to 'posts' table.\n";
    } else {
        echo "'tags' column already exists in 'posts' table.\n";
    }
    
    // Check if column already exists for projects
    $projCols = $db->query("PRAGMA table_info(projects)")->fetchAll(PDO::FETCH_ASSOC);
    $projTagsExists = false;
    foreach ($projCols as $col) {
        if ($col['name'] === 'tags') {
            $projTagsExists = true;
            break;
        }
    }
    
    if (!$projTagsExists) {
        $db->exec("ALTER TABLE projects ADD COLUMN tags TEXT");
        echo "Added 'tags' column to 'projects' table.\n";
    } else {
        echo "'tags' column already exists in 'projects' table.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
