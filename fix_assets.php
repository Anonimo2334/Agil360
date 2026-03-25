<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));

$count = 0;
foreach ($files as $file) {
    if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Match src="/images/something.ext"
        $newContent = preg_replace('/src="\/images\/([^"]+)"/', 'src="{{ asset(\'images/$1\') }}"', $content);
        
        if ($newContent !== null && $newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            $count++;
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Total files updated: $count\n";
