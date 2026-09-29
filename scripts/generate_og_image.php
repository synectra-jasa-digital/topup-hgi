<?php

$width = 1200;
$height = 630;

$im = imagecreatetruecolor($width, $height);
imagealphablending($im, true);
imagesavealpha($im, true);

// Colors
$bgDark     = imagecolorallocate($im, 11, 15, 25);     // #0B0F19
$bgBlue     = imagecolorallocate($im, 30, 58, 138);    // #1E3A8A
$bgSlate    = imagecolorallocate($im, 30, 41, 59);     // #1E293B
$white      = imagecolorallocate($im, 255, 255, 255);
$slate200   = imagecolorallocate($im, 226, 232, 240);  // #E2E8F0
$slate400   = imagecolorallocate($im, 148, 163, 184);  // #94A3B8
$bluePrimary= imagecolorallocate($im, 37, 99, 235);    // #2563EB
$blueAccent = imagecolorallocate($im, 56, 189, 248);   // #38BDF8 (cyan)
$emeraldAcc = imagecolorallocate($im, 52, 211, 153);   // #34D399 (green accent)
$goldAccent = imagecolorallocate($im, 251, 191, 36);   // #FBBF24 (gold coin)

// 1. Draw Linear Background Gradient (Top-Left to Bottom-Right)
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $factorX = $x / $width;
        $factorY = $y / $height;
        $mix = ($factorX + $factorY) / 2.0;

        // Base gradient: Dark Navy (#0B0F19) -> Rich Slate (#151D2A) -> Deep Blue (#1E3A8A)
        $r = (int) ((1 - $mix) * 11 + $mix * 25);
        $g = (int) ((1 - $mix) * 15 + $mix * 45);
        $b = (int) ((1 - $mix) * 25 + $mix * 110);

        // Add radial glow on top right (x=950, y=200)
        $dist = sqrt(pow($x - 950, 2) + pow($y - 180, 2));
        if ($dist < 450) {
            $glow = (1 - ($dist / 450));
            $r = (int) min(255, $r + $glow * 30);
            $g = (int) min(255, $g + $glow * 60);
            $b = (int) min(255, $b + $glow * 140);
        }

        // Add radial glow on bottom left (x=200, y=500)
        $distLeft = sqrt(pow($x - 200, 2) + pow($y - 500, 2));
        if ($distLeft < 400) {
            $glowL = (1 - ($distLeft / 400));
            $r = (int) min(255, $r + $glowL * 20);
            $g = (int) min(255, $g + $glowL * 40);
            $b = (int) min(255, $b + $glowL * 90);
        }

        $col = imagecolorallocate($im, (int)$r, (int)$g, (int)$b);
        imagesetpixel($im, $x, $y, $col);
    }
}

// 2. Draw Subtle Grid Pattern
$gridColor = imagecolorallocatealpha($im, 255, 255, 255, 120); // semi transparent
for ($gx = 0; $gx < $width; $gx += 40) {
    imageline($im, $gx, 0, $gx, $height, $gridColor);
}
for ($gy = 0; $gy < $height; $gy += 40) {
    imageline($im, 0, $gy, $width, $gy, $gridColor);
}

// Fonts
$fontBold = 'C:\\Windows\\Fonts\\segoeuib.ttf';
$fontReg  = 'C:\\Windows\\Fonts\\segoeui.ttf';
if (!file_exists($fontBold)) $fontBold = 'C:\\Windows\\Fonts\\arialbd.ttf';
if (!file_exists($fontReg))  $fontReg  = 'C:\\Windows\\Fonts\\arial.ttf';

// Helper for Rounded Rectangles with Alpha
function drawRoundedRectAlpha($im, $x1, $y1, $x2, $y2, $radius, $color, $borderColor = null) {
    // Draw fill
    imagefilledrectangle($im, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($im, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($im, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);

    if ($borderColor !== null) {
        imagesetthickness($im, 2);
        imagerectangle($im, $x1 + $radius, $y1, $x2 - $radius, $y2, $borderColor);
        imagerectangle($im, $x1, $y1 + $radius, $x2, $y2 - $radius, $borderColor);
        imagearc($im, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $borderColor);
        imagearc($im, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $borderColor);
        imagearc($im, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $borderColor);
        imagearc($im, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $borderColor);
        imagesetthickness($im, 1);
    }
}

// 3. Draw Left Glass Card Container
$cardBg = imagecolorallocatealpha($im, 15, 23, 42, 35); // Glass dark slate opacity
$cardBorder = imagecolorallocatealpha($im, 255, 255, 255, 100);
drawRoundedRectAlpha($im, 60, 50, 1140, 580, 24, $cardBg, $cardBorder);

// 4. Badge Pill (AYONG STORE - OFFICIAL TOP UP GATEWAY)
$badgeBg = imagecolorallocatealpha($im, 37, 99, 235, 40); // Blue glass
$badgeBorder = imagecolorallocatealpha($im, 56, 189, 248, 80); // Cyan border
drawRoundedRectAlpha($im, 100, 95, 590, 140, 12, $badgeBg, $badgeBorder);

// Dot indicator inside badge
imagefilledellipse($im, 125, 117, 12, 12, $emeraldAcc);
imagettftext($im, 14, 0, 145, 123, $blueAccent, $fontBold, 'AYONG STORE');
imagettftext($im, 14, 0, 280, 123, $slate200, $fontReg, '— OFFICIAL TOP UP GATEWAY');

// 5. Main Headline
$title1 = 'Top Up Koin Emas';
$title2 = '& Bongkar Chip';
imagettftext($im, 40, 0, 100, 215, $white, $fontBold, $title1);
imagettftext($im, 40, 0, 100, 275, $goldAccent, $fontBold, $title2);

// 6. Subtitle
$sub1 = 'Higgs Domino Island & Global • Murah, Aman & 24 Jam';
imagettftext($im, 20, 0, 100, 335, $slate200, $fontReg, $sub1);

// 7. Feature Badges (3 Pill Cards)
$features = [
    ['icon' => '[+] ', 'text' => 'Proses 1-3 Menit', 'col' => $emeraldAcc],
    ['icon' => '[v] ', 'text' => '100% Legal & Tanpa Password', 'col' => $blueAccent],
    ['icon' => '[$] ', 'text' => 'Transfer Bank & QRIS', 'col' => $goldAccent],
];

$featX = 100;
$featY = 380;
foreach ($features as $f) {
    $fBox = imagettfbbox(15, 0, $fontBold, $f['icon'] . $f['text']);
    $fW = abs($fBox[2] - $fBox[0]) + 36;
    
    $fBg = imagecolorallocatealpha($im, 30, 41, 59, 50);
    $fBrd = imagecolorallocatealpha($im, 255, 255, 255, 110);
    drawRoundedRectAlpha($im, $featX, $featY, $featX + $fW, $featY + 44, 10, $fBg, $fBrd);
    
    imagettftext($im, 14, 0, $featX + 18, $featY + 28, $f['col'], $fontBold, $f['text']);
    $featX += $fW + 16;
}

// 8. Footer URL
imagettftext($im, 18, 0, 100, 520, $slate400, $fontBold, 'WWW.AYONGSTORE.COM');
imagettftext($im, 15, 0, 380, 520, $emeraldAcc, $fontReg, '• Online Status 24/7');

// 9. Right Side Logo Glass Card
$logoPath = 'public/assets/uploads/store/1788851251_3a2a4e79d6f316b1143f.png';
if (!file_exists($logoPath)) {
    $logoPath = 'stitch/ayong_store_logo/screen.png';
}

if (file_exists($logoPath)) {
    $logoImg = imagecreatefrompng($logoPath);
    if ($logoImg) {
        $lw = imagesx($logoImg);
        $lh = imagesy($logoImg);
        
        // Draw white rounded background card for logo
        $logoCardBg = imagecolorallocatealpha($im, 255, 255, 255, 10); // Subtle glass
        $logoCardBrd = imagecolorallocatealpha($im, 255, 255, 255, 80);
        
        $cardX1 = 700;
        $cardY1 = 120;
        $cardX2 = 1080;
        $cardY2 = 480;
        drawRoundedRectAlpha($im, $cardX1, $cardY1, $cardX2, $cardY2, 20, $logoCardBg, $logoCardBrd);
        
        // Target logo size inside card
        $maxW = 340;
        $maxH = 260;
        
        $scale = min($maxW / $lw, $maxH / $lh);
        $nw = (int)($lw * $scale);
        $nh = (int)($lh * $scale);
        
        $dstX = (int)($cardX1 + ($cardX2 - $cardX1 - $nw) / 2);
        $dstY = (int)($cardY1 + ($cardY2 - $cardY1 - $nh) / 2);
        
        imagecopyresampled($im, $logoImg, $dstX, $dstY, 0, 0, $nw, $nh, $lw, $lh);
        imagedestroy($logoImg);
    }
}

// Create destination dir
$outDir = 'public/assets/img';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

// Save PNG & JPG
$pngPath = $outDir . '/og-image.png';
$jpgPath = $outDir . '/og-image.jpg';

imagepng($im, $pngPath, 8);
imagejpeg($im, $jpgPath, 92);
imagedestroy($im);

echo "✓ OG Image successfully generated!\n";
echo "  - PNG: $pngPath\n";
echo "  - JPG: $jpgPath\n";
