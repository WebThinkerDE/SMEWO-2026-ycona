<?php
/**
 * Compile .po file to .mo file.
 * Usage: php compile-mo.php
 *
 * Run this script after editing the .po file to regenerate the .mo binary.
 * Example: C:\xampp\php\php.exe compile-mo.php
 */

$po_file = __DIR__ . '/de_DE.po';
$mo_file = __DIR__ . '/de_DE.mo';

if ( ! file_exists( $po_file ) ) {
    echo "Error: {$po_file} not found.\n";
    exit(1);
}

$entries = [];
$current_msgctxt = '';
$current_msgid   = '';
$current_msgstr  = '';
$current_key     = '';

$lines = file( $po_file, FILE_IGNORE_NEW_LINES );

foreach ( $lines as $line ) {
    $line = trim( $line );

    // Skip comments and empty lines
    if ( $line === '' || $line[0] === '#' ) {
        // Save previous entry
        if ( $current_key === 'msgstr' && $current_msgid !== '' ) {
            $key = $current_msgctxt !== '' ? $current_msgctxt . "\x04" . $current_msgid : $current_msgid;
            $entries[ $key ] = $current_msgstr;
        }
        $current_msgctxt = '';
        $current_msgid   = '';
        $current_msgstr  = '';
        $current_key     = '';
        continue;
    }

    if ( strpos( $line, 'msgctxt ' ) === 0 ) {
        // Save previous entry
        if ( $current_key === 'msgstr' && $current_msgid !== '' ) {
            $key = $current_msgctxt !== '' ? $current_msgctxt . "\x04" . $current_msgid : $current_msgid;
            $entries[ $key ] = $current_msgstr;
        }
        $current_msgctxt = '';
        $current_msgid   = '';
        $current_msgstr  = '';
        $current_key     = 'msgctxt';
        $current_msgctxt = parse_po_string( substr( $line, 8 ) );
    } elseif ( strpos( $line, 'msgid ' ) === 0 ) {
        // Save previous entry if starting new msgid without msgctxt
        if ( $current_key === 'msgstr' && $current_msgid !== '' ) {
            $key = $current_msgctxt !== '' ? $current_msgctxt . "\x04" . $current_msgid : $current_msgid;
            $entries[ $key ] = $current_msgstr;
            $current_msgctxt = '';
        }
        $current_key   = 'msgid';
        $current_msgid = parse_po_string( substr( $line, 6 ) );
    } elseif ( strpos( $line, 'msgstr ' ) === 0 ) {
        $current_key    = 'msgstr';
        $current_msgstr = parse_po_string( substr( $line, 7 ) );
    } elseif ( $line[0] === '"' ) {
        // Continuation line
        $str = parse_po_string( $line );
        if ( $current_key === 'msgctxt' ) {
            $current_msgctxt .= $str;
        } elseif ( $current_key === 'msgid' ) {
            $current_msgid .= $str;
        } elseif ( $current_key === 'msgstr' ) {
            $current_msgstr .= $str;
        }
    }
}

// Save last entry
if ( $current_key === 'msgstr' && $current_msgid !== '' ) {
    $key = $current_msgctxt !== '' ? $current_msgctxt . "\x04" . $current_msgid : $current_msgid;
    $entries[ $key ] = $current_msgstr;
}

// Also save the header (empty msgid)
if ( $current_key === 'msgstr' && $current_msgid === '' && $current_msgstr !== '' ) {
    $entries[''] = $current_msgstr;
}

// Re-parse to get header entry properly
$header_msgstr = '';
$in_header = false;
foreach ( $lines as $line ) {
    $line = trim( $line );
    if ( $line === 'msgid ""' ) {
        $in_header = true;
        continue;
    }
    if ( $in_header && strpos( $line, 'msgstr ' ) === 0 ) {
        $header_msgstr = parse_po_string( substr( $line, 7 ) );
        continue;
    }
    if ( $in_header && $line !== '' && $line[0] === '"' ) {
        $header_msgstr .= parse_po_string( $line );
        continue;
    }
    if ( $in_header && ( $line === '' || $line[0] === '#' ) ) {
        break;
    }
}
$entries[''] = $header_msgstr;

// Build .mo file
ksort( $entries );

$count = count( $entries );
$header_size = 28;
$hash_table_size = 0;
$offset_original = $header_size;
$offset_translation = $offset_original + $count * 8;
$offset_hash = $offset_translation + $count * 8;
$offset_strings = $offset_hash + $hash_table_size;

$originals    = [];
$translations = [];

// Calculate original string offsets
$current_offset = $offset_strings;
foreach ( $entries as $original => $translation ) {
    $originals[] = [ 'length' => strlen( $original ), 'offset' => $current_offset ];
    $current_offset += strlen( $original ) + 1; // +1 for null byte
}

// Calculate translation string offsets
foreach ( $entries as $original => $translation ) {
    $translations[] = [ 'length' => strlen( $translation ), 'offset' => $current_offset ];
    $current_offset += strlen( $translation ) + 1;
}

// Write .mo file
$mo = '';

// Magic number (little-endian)
$mo .= pack( 'V', 0x950412de );
// Revision
$mo .= pack( 'V', 0 );
// Number of strings
$mo .= pack( 'V', $count );
// Offset of original strings table
$mo .= pack( 'V', $offset_original );
// Offset of translation strings table
$mo .= pack( 'V', $offset_translation );
// Size of hash table
$mo .= pack( 'V', $hash_table_size );
// Offset of hash table
$mo .= pack( 'V', $offset_hash );

// Original strings table
foreach ( $originals as $entry ) {
    $mo .= pack( 'V', $entry['length'] );
    $mo .= pack( 'V', $entry['offset'] );
}

// Translation strings table
foreach ( $translations as $entry ) {
    $mo .= pack( 'V', $entry['length'] );
    $mo .= pack( 'V', $entry['offset'] );
}

// Original strings
foreach ( array_keys( $entries ) as $original ) {
    $mo .= $original . "\0";
}

// Translation strings
foreach ( $entries as $translation ) {
    $mo .= $translation . "\0";
}

file_put_contents( $mo_file, $mo );
echo "Successfully compiled {$mo_file}\n";
echo "Total entries: {$count}\n";

function parse_po_string( $str ) {
    $str = trim( $str );
    if ( $str[0] === '"' && substr( $str, -1 ) === '"' ) {
        $str = substr( $str, 1, -1 );
    }
    // Process escape sequences
    $str = str_replace( '\\n', "\n", $str );
    $str = str_replace( '\\t', "\t", $str );
    $str = str_replace( '\\"', '"', $str );
    $str = str_replace( '\\\\', '\\', $str );
    return $str;
}
