import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

# Canvas Dimensions (OG Image Standard: 1200 x 630)
WIDTH = 1200
HEIGHT = 630

# Initialize canvas
im = Image.new('RGBA', (WIDTH, HEIGHT), (11, 15, 25, 255))

# 1. High-End Deep Navy Gradient Background
bg = Image.new('RGBA', (WIDTH, HEIGHT))
for y in range(HEIGHT):
    for x in range(WIDTH):
        fx = x / WIDTH
        fy = y / HEIGHT
        mix = (fx * 0.4 + fy * 0.6)
        
        # Base colors: Deep Night Slate (#080C14) -> Rich Gaming Navy (#0F172A) -> Blue 900 (#1E3A8A)
        r = int((1 - mix) * 8 + mix * 24)
        g = int((1 - mix) * 12 + mix * 48)
        b = int((1 - mix) * 20 + mix * 115)
        
        # Primary Radial Glow (Right side around Logo Card x=920, y=315)
        dist_right = math.sqrt((x - 920)**2 + (y - 315)**2)
        if dist_right < 520:
            glow_r = (1.0 - (dist_right / 520.0)) ** 2.2
            r = min(255, int(r + glow_r * 45))
            g = min(255, int(g + glow_r * 90))
            b = min(255, int(b + glow_r * 180))
            
        # Golden Ambient Glow (Top Right Corner x=1100, y=80)
        dist_gold = math.sqrt((x - 1100)**2 + (y - 80)**2)
        if dist_gold < 380:
            glow_g = (1.0 - (dist_gold / 380.0)) ** 2
            r = min(255, int(r + glow_g * 65))
            g = min(255, int(g + glow_g * 45))
            b = min(255, int(b + glow_g * 10))

        # Cyan Accent Glow (Bottom Left Corner x=100, y=550)
        dist_cyan = math.sqrt((x - 100)**2 + (y - 550)**2)
        if dist_cyan < 420:
            glow_c = (1.0 - (dist_cyan / 420.0)) ** 2
            r = min(255, int(r + glow_c * 10))
            g = min(255, int(g + glow_c * 50))
            b = min(255, int(b + glow_c * 90))
            
        bg.putpixel((x, y), (r, g, b, 255))

im.paste(bg, (0, 0))

# 2. Add Decorative Cyber Grid & Light Rays
grid_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
g_draw = ImageDraw.Draw(grid_layer)
grid_color = (255, 255, 255, 10) # 4% opacity grid

for gx in range(0, WIDTH, 40):
    g_draw.line([(gx, 0), (gx, HEIGHT)], fill=grid_color, width=1)
for gy in range(0, HEIGHT, 40):
    g_draw.line([(0, gy), (WIDTH, gy)], fill=grid_color, width=1)

im = Image.alpha_composite(im, grid_layer)

# 3. Load Fonts
font_dir = "C:\\Windows\\Fonts"
try:
    font_badge   = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 14)
    font_title1  = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 48)
    font_title2  = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 42)
    font_sub     = ImageFont.truetype(os.path.join(font_dir, "segoeui.ttf"), 22)
    font_pill    = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 16)
    font_payment = ImageFont.truetype(os.path.join(font_dir, "segoeui.ttf"), 18)
    font_domain  = ImageFont.truetype(os.path.join(font_dir, "segoeuib.ttf"), 20)
except Exception:
    font_badge = font_title1 = font_title2 = font_sub = font_pill = font_payment = font_domain = ImageFont.load_default()

draw = ImageDraw.Draw(im)
LEFT_X = 85

# --- 4. Top Status Badge ---
badge_y = 80
badge_text = "AYONG STORE  •  24 JAM ONLINE"
badge_bbox = font_badge.getbbox(badge_text)
badge_w = (badge_bbox[2] - badge_bbox[0]) + 44

badge_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
b_draw = ImageDraw.Draw(badge_layer)

# Badge background pill
b_draw.rounded_rectangle(
    [(LEFT_X, badge_y), (LEFT_X + badge_w, badge_y + 36)],
    radius=18,
    fill=(30, 58, 138, 120),       # Blue container opacity
    outline=(56, 189, 248, 180),   # Cyan glow border
    width=1
)

im = Image.alpha_composite(im, badge_layer)
draw = ImageDraw.Draw(im)

# Green pulsing live status dot
draw.ellipse([(LEFT_X + 16, badge_y + 13), (LEFT_X + 26, badge_y + 23)], fill=(34, 197, 94, 255))
draw.text((LEFT_X + 34, badge_y + 8), badge_text, font=font_badge, fill=(240, 246, 255, 255))

# --- 5. Main Headlines ---
title1_y = 145
draw.text((LEFT_X, title1_y), "Top Up & Bongkar", font=font_title1, fill=(255, 255, 255, 255))

title2_y = title1_y + 62

# Create Golden Gradient for "Koin Higgs Domino"
gold_text = "Koin Higgs Domino"
gold_bbox = font_title2.getbbox(gold_text)
gold_w = gold_bbox[2] - gold_bbox[0]
gold_h = gold_bbox[3] - gold_bbox[1] + 10

# Draw gold gradient text using an mask layer
gold_txt_img = Image.new('RGBA', (gold_w + 20, gold_h + 20), (0,0,0,0))
gold_draw = ImageDraw.Draw(gold_txt_img)
gold_draw.text((0, 0), gold_text, font=font_title2, fill=(255, 255, 255, 255))

# Create gold gradient fill
gold_fill = Image.new('RGBA', (gold_w + 20, gold_h + 20))
for gy in range(gold_h + 20):
    factor = gy / float(gold_h + 20)
    # Gold gradient (#FDE047 yellow -> #F59E0B amber -> #D97706 orange)
    gr = int((1 - factor) * 253 + factor * 217)
    gg = int((1 - factor) * 224 + factor * 119)
    gb = int((1 - factor) * 71  + factor * 6)
    for gx in range(gold_w + 20):
        gold_fill.putpixel((gx, gy), (gr, gg, gb, 255))

gold_txt_img = Image.composite(gold_fill, Image.new('RGBA', (gold_w + 20, gold_h + 20), (0,0,0,0)), gold_txt_img.split()[3])
im.paste(gold_txt_img, (LEFT_X, title2_y), gold_txt_img)

# Subtitle line
sub_y = title2_y + 68
draw.text(
    (LEFT_X, sub_y),
    "Higgs Domino Island & Global  •  Koin MD & Ungu",
    font=font_sub,
    fill=(203, 213, 225, 255) # Slate 300
)

# --- 6. Feature Pill Cards ---
pill_y = sub_y + 55
pills = [
    {"label": "⚡ Transaksi Otomatis", "border": (56, 189, 248, 160), "bg": (15, 23, 42, 180), "txt": (56, 189, 248, 255)},
    {"label": "🛡️ Tanpa Password", "border": (52, 211, 153, 160), "bg": (15, 23, 42, 180), "txt": (52, 211, 153, 255)},
    {"label": "💎 Rate Terbaik", "border": (251, 191, 36, 160), "bg": (15, 23, 42, 180), "txt": (251, 191, 36, 255)},
]

curr_px = LEFT_X
for p in pills:
    p_bbox = font_pill.getbbox(p["label"])
    p_w = (p_bbox[2] - p_bbox[0]) + 32
    p_h = 42
    
    p_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
    p_draw = ImageDraw.Draw(p_layer)
    p_draw.rounded_rectangle(
        [(curr_px, pill_y), (curr_px + p_w, pill_y + p_h)],
        radius=10,
        fill=p["bg"],
        outline=p["border"],
        width=1
    )
    im = Image.alpha_composite(im, p_layer)
    draw = ImageDraw.Draw(im)
    
    draw.text((curr_px + 16, pill_y + 10), p["label"], font=font_pill, fill=p["txt"])
    curr_px += p_w + 14

# --- 7. Payment Bar ---
pay_y = pill_y + 64
pay_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
p_draw = ImageDraw.Draw(pay_layer)

p_draw.rounded_rectangle(
    [(LEFT_X, pay_y), (LEFT_X + 575, pay_y + 48)],
    radius=10,
    fill=(15, 23, 42, 160),
    outline=(51, 65, 85, 200),
    width=1
)
im = Image.alpha_composite(im, pay_layer)
draw = ImageDraw.Draw(im)

draw.text(
    (LEFT_X + 20, pay_y + 12),
    "Pembayaran: QRIS All Payment & Transfer Bank Resmi",
    font=font_payment,
    fill=(226, 232, 240, 255)
)

# --- 8. Domain Tag Footer ---
domain_y = pay_y + 72
draw.text((LEFT_X, domain_y), "ayongstore.com", font=font_domain, fill=(56, 189, 248, 255))
draw.text((LEFT_X + 175, domain_y), "— Platform Resmi Top Up Game", font=font_sub, fill=(100, 116, 139, 255))


# --- 9. Right Side Glass Card with Elevated Logo ---
card_x1 = 720
card_y1 = 110
card_w  = 400
card_h  = 410
card_x2 = card_x1 + card_w
card_y2 = card_y1 + card_h

# Draw Outer Glass Glow
glow_card = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
g_draw = ImageDraw.Draw(glow_card)
g_draw.rounded_rectangle(
    [(card_x1 - 4, card_y1 - 4), (card_x2 + 4, card_y2 + 4)],
    radius=24,
    fill=(37, 99, 235, 30),
    outline=(56, 189, 248, 100),
    width=1
)
im = Image.alpha_composite(im, glow_card)

# Draw Main Glass Container
glass_card = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
c_draw = ImageDraw.Draw(glass_card)

c_draw.rounded_rectangle(
    [(card_x1, card_y1), (card_x2, card_y2)],
    radius=22,
    fill=(15, 23, 42, 190),       # Dark slate container
    outline=(51, 65, 85, 240),    # Slate 700 border
    width=1
)
im = Image.alpha_composite(im, glass_card)
draw = ImageDraw.Draw(im)

# Load Logo
logo_path = "public/assets/uploads/store/1788851251_3a2a4e79d6f316b1143f.png"
if not os.path.exists(logo_path):
    logo_path = "stitch/ayong_store_logo/screen.png"

if os.path.exists(logo_path):
    logo_img = Image.open(logo_path).convert("RGBA")
    lw, lh = logo_img.size
    
    max_lw = 320
    max_lh = 220
    scale = min(max_lw / lw, max_lh / lh)
    nw = int(lw * scale)
    nh = int(lh * scale)
    
    logo_scaled = logo_img.resize((nw, nh), Image.Resampling.LANCZOS)
    
    # Position logo inside upper area of card
    logo_x = card_x1 + (card_w - nw) // 2
    logo_y = card_y1 + 80
    
    im.paste(logo_scaled, (logo_x, logo_y), logo_scaled)

# Decorative trust badge inside card bottom
trust_box_y = card_y1 + 290
trust_bg = Image.new('RGBA', (WIDTH, HEIGHT), (0,0,0,0))
t_draw = ImageDraw.Draw(trust_bg)
t_draw.rounded_rectangle(
    [(card_x1 + 30, trust_box_y), (card_x2 - 30, trust_box_y + 70)],
    radius=12,
    fill=(30, 58, 138, 140),
    outline=(59, 130, 246, 160),
    width=1
)
im = Image.alpha_composite(im, trust_bg)
draw = ImageDraw.Draw(im)

# Trust badge text
draw.text((card_x1 + 65, trust_box_y + 14), "VERIFIED TOP UP STORE", font=font_pill, fill=(240, 246, 255, 255))
draw.text((card_x1 + 75, trust_box_y + 38), "Layanan Cepat • Transaksi Aman", font=font_payment, fill=(147, 197, 253, 255))


# --- 10. Save Image Files ---
output_dir = "public/assets/img"
os.makedirs(output_dir, exist_ok=True)

png_path = os.path.join(output_dir, "og-image.png")
jpg_path = os.path.join(output_dir, "og-image.jpg")

im.convert("RGB").save(png_path, "PNG", optimize=True)
im.convert("RGB").save(jpg_path, "JPEG", quality=93, optimize=True)

print("[OK] New high-converting attractive OG Image successfully generated!")
print(f"  - PNG: {png_path}")
print(f"  - JPG: {jpg_path}")
