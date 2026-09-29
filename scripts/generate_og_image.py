import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

# Canvas Dimensions
WIDTH = 1200
HEIGHT = 630

# Initialize canvas
im = Image.new('RGBA', (WIDTH, HEIGHT), (15, 23, 42, 255))
draw = ImageDraw.Draw(im)

# 1. Background Gradient & Glow
# Create background gradient from #0B0F19 to #1E3A8A
bg = Image.new('RGBA', (WIDTH, HEIGHT))
bg_draw = ImageDraw.Draw(bg)

for y in range(HEIGHT):
    for x in range(WIDTH):
        fx = x / WIDTH
        fy = y / HEIGHT
        mix = (fx + fy) / 2.0
        
        # Base slate dark gradient (#0F172A -> #1E3A8A)
        r = int((1 - mix) * 15 + mix * 30)
        g = int((1 - mix) * 23 + mix * 58)
        b = int((1 - mix) * 42 + mix * 138)
        
        # Radial glow around right logo position (x=900, y=315)
        dist = math.sqrt((x - 900)**2 + (y - 315)**2)
        if dist < 480:
            glow = (1.0 - (dist / 480.0)) ** 2
            r = min(255, int(r + glow * 35))
            g = min(255, int(g + glow * 70))
            b = min(255, int(b + glow * 150))
            
        bg.putpixel((x, y), (r, g, b, 255))

im.paste(bg, (0, 0))
draw = ImageDraw.Draw(im)

# 2. Subtle Grid Lines (Direct Gaming Trust aesthetic)
grid_overlay = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
g_draw = ImageDraw.Draw(grid_overlay)
grid_color = (255, 255, 255, 12) # 5% opacity

for gx in range(0, WIDTH, 48):
    g_draw.line([(gx, 0), (gx, HEIGHT)], fill=grid_color, width=1)
for gy in range(0, HEIGHT, 48):
    g_draw.line([(0, gy), (WIDTH, gy)], fill=grid_color, width=1)

im = Image.alpha_composite(im, grid_overlay)
draw = ImageDraw.Draw(im)

# 3. Load Fonts
font_dir = "C:\\Windows\\Fonts"
try:
    font_badge = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 15)
    font_title1 = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 44)
    font_title2 = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 34)
    font_sub = ImageFont.truetype(os.path.join(font_dir, "segoeui.ttf"), 22)
    font_payment = ImageFont.truetype(os.path.join(font_dir, "segoeui.ttf"), 18)
    font_domain = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 18)
except Exception:
    font_badge = ImageFont.load_default()
    font_title1 = font_badge
    font_title2 = font_badge
    font_sub = font_badge
    font_payment = font_badge
    font_domain = font_badge

# 4. Draw Left Content Group
LEFT_X = 85

# --- Brand Pill Badge ---
badge_text = "AYONG STORE"
badge_bbox = font_badge.getbbox(badge_text)
badge_tw = badge_bbox[2] - badge_bbox[0]
badge_th = badge_bbox[3] - badge_bbox[1]

badge_pad_x = 20
badge_pad_y = 10
badge_w = badge_tw + (badge_pad_x * 2) + 24
badge_h = 38
badge_y = 90

# Draw Badge Pillow Background
badge_bg = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
b_draw = ImageDraw.Draw(badge_bg)
b_draw.rounded_rectangle(
    [(LEFT_X, badge_y), (LEFT_X + badge_w, badge_y + badge_h)],
    radius=8,
    fill=(37, 99, 235, 45), # rgba(37,99,235,0.18)
    outline=(59, 130, 246, 120), # rgba(59,130,246,0.47)
    width=1
)
im = Image.alpha_composite(im, badge_bg)
draw = ImageDraw.Draw(im)

# Dot indicator
draw.ellipse(
    [(LEFT_X + 16, badge_y + 14), (LEFT_X + 26, badge_y + 24)],
    fill=(34, 197, 94, 255) # Green status dot
)
draw.text(
    (LEFT_X + 36, badge_y + 9),
    badge_text,
    font=font_badge,
    fill=(255, 255, 255, 255)
)

# --- Headline ---
title_y1 = 160
draw.text(
    (LEFT_X, title_y1),
    "Top Up & Bongkar Koin Emas",
    font=font_title1,
    fill=(255, 255, 255, 255)
)

title_y2 = title_y1 + 58
draw.text(
    (LEFT_X, title_y2),
    "Higgs Domino Island & Global",
    font=font_title2,
    fill=(147, 197, 253, 255) # Soft Sky Blue #93C5FD
)

# --- Subtitle / Product Highlights ---
sub_y = title_y2 + 65
draw.text(
    (LEFT_X, sub_y),
    "Koin MD & Ungu  •  Nominal 200M s/d 10B  •  Proses Cepat",
    font=font_sub,
    fill=(203, 213, 225, 255) # Slate 300 #CBD5E1
)

# --- Payment Methods Bar ---
pay_y = sub_y + 65
pay_bg = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
p_draw = ImageDraw.Draw(pay_bg)
p_draw.rounded_rectangle(
    [(LEFT_X, pay_y), (LEFT_X + 570, pay_y + 54)],
    radius=12,
    fill=(15, 23, 42, 160), # Dark slate surface
    outline=(51, 65, 85, 200), # Slate 700 border
    width=1
)
im = Image.alpha_composite(im, pay_bg)
draw = ImageDraw.Draw(im)

draw.text(
    (LEFT_X + 24, pay_y + 15),
    "Pembayaran: BCA  •  BRI  •  Mandiri  •  BNI  •  QRIS Instant",
    font=font_payment,
    fill=(226, 232, 240, 255)
)

# --- Domain Footer ---
domain_y = pay_y + 90
draw.text(
    (LEFT_X, domain_y),
    "ayongstore.com",
    font=font_domain,
    fill=(100, 116, 139, 255) # Slate 500
)

# 5. Right Side Logo Card
logo_card_x1 = 730
logo_card_y1 = 120
logo_card_w = 385
logo_card_h = 390
logo_card_x2 = logo_card_x1 + logo_card_w
logo_card_y2 = logo_card_y1 + logo_card_h

# Draw Glass Card for Logo
logo_bg = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
l_draw = ImageDraw.Draw(logo_bg)
l_draw.rounded_rectangle(
    [(logo_card_x1, logo_card_y1), (logo_card_x2, logo_card_y2)],
    radius=20,
    fill=(15, 23, 42, 140), # Dark surface transparency
    outline=(51, 65, 85, 220), # Slate border
    width=1
)
im = Image.alpha_composite(im, logo_bg)
draw = ImageDraw.Draw(im)

# Load Logo File
logo_path = "public/assets/uploads/store/1788851251_3a2a4e79d6f316b1143f.png"
if not os.path.exists(logo_path):
    logo_path = "stitch/ayong_store_logo/screen.png"

if os.path.exists(logo_path):
    logo_img = Image.open(logo_path).convert("RGBA")
    lw, lh = logo_img.size
    
    # Scale Logo maintaining aspect ratio
    max_lw = 310
    max_lh = 220
    scale = min(max_lw / lw, max_lh / lh)
    nw = int(lw * scale)
    nh = int(lh * scale)
    
    logo_scaled = logo_img.resize((nw, nh), Image.Resampling.LANCZOS)
    
    # Center logo inside card
    logo_x = logo_card_x1 + (logo_card_w - nw) // 2
    logo_y = logo_card_y1 + (logo_card_h - nh) // 2
    
    im.paste(logo_scaled, (logo_x, logo_y), logo_scaled)

# 6. Save Output Files
output_dir = "public/assets/img"
os.makedirs(output_dir, exist_ok=True)

png_path = os.path.join(output_dir, "og-image.png")
jpg_path = os.path.join(output_dir, "og-image.jpg")

im.convert("RGB").save(png_path, "PNG", optimize=True)
im.convert("RGB").save(jpg_path, "JPEG", quality=92, optimize=True)

print(f"[OK] Clean non-slop OG Image successfully generated!")
print(f"  - PNG: {png_path}")
print(f"  - JPG: {jpg_path}")
