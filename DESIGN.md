---
name: PhoneStore Design System
description: Flagship Smartphone E-Commerce Experience (Apple & Samsung Store Inspired)
colors:
  primary: "#2563eb"
  primary-hover: "#1d4ed8"
  primary-light: "#eff6ff"
  accent-red: "#e11d48"
  accent-rose: "#f43f5e"
  neutral-bg: "#f8fafc"
  neutral-card: "#ffffff"
  neutral-surface: "#f1f5f9"
  neutral-border: "#e2e8f0"
  neutral-text: "#0f172a"
  neutral-muted: "#64748b"
  neutral-dark: "#020617"
typography:
  display:
    fontFamily: '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif'
    fontSize: "clamp(2rem, 5vw, 3rem)"
    fontWeight: 900
    lineHeight: 1.1
    letterSpacing: "-0.03em"
  headline:
    fontFamily: '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif'
    fontSize: "clamp(1.5rem, 3vw, 2.25rem)"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  title:
    fontFamily: '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif'
    fontSize: "1.125rem"
    fontWeight: 800
    lineHeight: 1.3
    letterSpacing: "-0.01em"
  body:
    fontFamily: '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif'
    fontSize: "0.875rem"
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif'
    fontSize: "0.6875rem"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "0.05em"
rounded:
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "20px"
  "2xl": "24px"
  "3xl": "32px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  "2xl": "48px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#ffffff"
    rounded: "{rounded.full}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  card-product:
    backgroundColor: "{colors.neutral-card}"
    rounded: "{rounded.3xl}"
    padding: "20px"
  input-search:
    backgroundColor: "{colors.neutral-surface}"
    textColor: "{colors.neutral-text}"
    rounded: "{rounded.full}"
    padding: "10px 20px"
---

# Design System: PhoneStore

## Overview

**Creative North Star: "The Flagship Showcase"**

PhoneStore là hệ thống thiết kế thương mại điện tử chuyên biệt cho điện thoại di động thông minh cao cấp. Lấy cảm hứng từ ngôn ngữ thị giác tối giản của Apple Store và trải nghiệm công nghệ sinh động của Samsung Experience Store, PhoneStore cân bằng hoàn hảo giữa tính sang trọng, tốc độ phản hồi tức thì và sự trong suốt, mượt mà (Glassmorphism & Micro-animations).

Giao diện đặt thiết bị và thông số kỹ thuật làm tâm điểm. Tông nền màu xám đá Slate dịu nhẹ làm nổi bật các khối sản phẩm 3D, trong khi các mảng màu điểm nhấn Electric Blue và Crimson Rose định hướng thị giác đến giá ưu đãi và các hành động chuyển đổi chính.

**Key Characteristics:**
- **Glassmorphism & Depth:** Thanh điều hướng và các card nổi sử dụng blur mờ (`backdrop-blur-md bg-white/85`) kết hợp viền mờ `border-slate-200/80` và bóng đổ đa tầng.
- **Micro-interactions:** Hiệu ứng hover nhấc nhẹ (`-translate-y-1.5`), bóng đổ lan tỏa `shadow-blue-500/10`, phóng to hình ảnh `scale-110` mượt mà.
- **Phân cấp typography dày dặn:** Sử dụng font `Plus Jakarta Sans` với các trọng số cực đại (800/900 font-black) tạo nét hiện đại, công nghệ và uy lực.

## Colors

Bảng màu mang phong cách Slate cao cấp, trung tính và chuẩn mực, tạo nền tảng vững chắc để tôn vinh hình ảnh các dòng flagship.

### Primary
- **Electric Sapphire** (`#2563eb` / `rgb(37, 99, 235)`): Màu chủ đạo cho thương hiệu, nút kêu gọi hành động (CTA), liên kết điều hướng và trạng thái active.
- **Deep Navy** (`#1d4ed8`): Trạng thái hover và nhấn của các thành phần tương tác.
- **Ice Blue Light** (`#eff6ff`): Nền phụ trợ cho các badge, chip danh mục, tag khuyến mãi Smember.

### Accent & Conversion
- **Crimson Rose** (`#e11d48` / `#f43f5e`): Đại diện cho mức giá niêm yết, ưu đãi giảm giá, badge flash sale và nút xóa/cảnh báo.

### Neutral
- **Slate Canvas** (`#f8fafc`): Nền toàn trang, tạo cảm giác sạch sẽ và chuyên nghiệp hơn màu trắng thuần.
- **Card Surface** (`#ffffff`): Nền các container, popup và card sản phẩm.
- **Zinc Border** (`#e2e8f0`): Đường viền tinh tế phân định các khối nội dung.
- **Midnight Text** (`#0f172a`): Màu chữ tiêu đề và văn bản chính, độ tương phản cao đạt chuẩn WCAG AAA.
- **Cool Slate Text** (`#64748b`): Màu chú thích, giá gốc gạch ngang và nhãn phụ.

### Named Rules
**The Rarity of Red Rule.** Màu đỏ Crimson (`#e11d48`) chỉ được sử dụng cho giá bán thực tế và mức % giảm giá, không bao giờ dùng làm màu nền cho toàn bộ nút bấm lớn để tránh gây áp lực thị giác.

## Typography

**Display Font:** "Plus Jakarta Sans", sans-serif  
**Body Font:** "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif  
**Mono Font:** ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas  

**Character:** Sự kết hợp hoàn hảo giữa nét hình học hiện đại, độ đậm dày dặn cho tiêu đề công nghệ và các khoảng hở quang học rộng mở cho nội dung đọc dễ dàng trên màn hình OLED.

### Hierarchy
- **Display** (Font-black 900, `clamp(2rem, 5vw, 3rem)`, line-height 1.1): Tiêu đề chính Hero Banner và khẩu hiệu flagship.
- **Headline** (Font-black 800, `1.5rem - 2.25rem`, line-height 1.2): Tiêu đề các mục lớn (Khám Phá Dòng Sản Phẩm, Giỏ Hàng, Bento Grid).
- **Title** (Font-extrabold 800, `1.125rem - 1.25rem`, line-height 1.3): Tên dòng sản phẩm trong trang chi tiết và thẻ card.
- **Body** (Font-medium 500, `0.875rem`, line-height 1.5): Đoạn văn mô tả, thông số chi tiết, chính sách bán hàng.
- **Label** (Font-black 800, `0.6875rem`, letter-spacing 0.05em, UPPERCASE): Chip danh mục, badge khuyến mãi, nhãn trạng thái tồn kho.

### Named Rules
**The High-Contrast Weight Rule.** Luôn ghép cặp tiêu đề siêu đậm (Font-black 800/900) với văn bản phụ trợ màu nhạt (Slate-400/500) để tạo trật tự thị giác rõ ràng chỉ trong 1 cái liếc mắt.

## Layout

Hệ thống lưới linh hoạt dựa trên container 12 cột chuẩn, với chiều rộng giới hạn tối đa `max-w-7xl` (1280px) và `max-w-6xl` (1152px) cho giỏ hàng.

- **Khoảng cách nhịp điệu (Rhythm):** Sử dụng các bước nhảy 4px / 8px / 16px / 24px / 32px / 48px / 64px (`space-y-6`, `space-y-10`, `gap-7`).
- **Responsive Breakpoints:**
  - `sm` (640px): Chuyển đổi từ 1 cột sang 2 cột sản phẩm.
  - `lg` (1024px): Bố cục 4 cột sản phẩm, 2 cột giỏ hàng (8 cột danh sách + 4 cột tổng kết dính).

## Elevation & Depth

PhoneStore áp dụng triết lý **Hybrid Elevation**: Kết hợp giữa bề mặt phẳng tinh khiết và bóng đổ khuếch tán đa tầng (Diffuse Layered Shadows) khi người dùng tương tác.

### Shadow Vocabulary
- **Subtle Surface** (`box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05)`): Trạng thái nghỉ của các thẻ box sản phẩm và thanh công cụ.
- **Floating Glass** (`box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07)`): Dành cho Sticky Glass Navbar và Live Search Dropdown.
- **Interactive Lift** (`box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08), 0 0 20px rgba(37, 99, 235, 0.1)`): Kích hoạt khi hover vào các thẻ sản phẩm hoặc nút CTA chính.

## Shapes

- **Bo góc đại dương (Generous Radii):** Các box sản phẩm và popup dropdown sử dụng bo góc lớn `rounded-3xl` (24px - 32px), mang lại cảm giác thiết bị cầm tay cao cấp.
- **Pill & Capsule:** Nút bấm hành động, ô tìm kiếm và badge sử dụng `rounded-full` (9999px) tối giản và thanh thoát.
- **Viền mịn:** Hầu hết các thành phần đều có đường viền bán trong suốt `border border-slate-200/80` để giữ cấu trúc vững chắc trên nền sáng.

## Components

### Buttons
- **Primary Action (Thêm giỏ hàng / Mua ngay):** Nền Gradient Electric Sapphire (`from-blue-600 to-indigo-600`), chữ trắng, bo tròn `rounded-full`, padding `12px 24px`, shadow `shadow-blue-500/25`. Khi hover: nhấc nhẹ `hover:-translate-y-0.5`.
- **Secondary / Ghost:** Nền `bg-slate-100` hoặc viền `border-slate-200`, chữ `text-slate-700`, hover đổi sang `bg-blue-50 text-blue-600`.

### Cards (Product Card Box)
- **Cấu trúc:** Bo góc `rounded-3xl`, nền gradient `from-white via-slate-50/50 to-slate-100/70`, viền `border-slate-200/90`.
- **Khung ảnh:** Nền kính `bg-white/90 p-4 rounded-2xl` hỗ trợ phóng to `scale-110` mượt mà khi hover.
- **Hộp khuyến mãi:** Chip màu pastel `bg-blue-50 text-blue-800` (Smember) và `bg-indigo-50 text-indigo-800` (S-Student).

### Inputs & Live Search
- **Thanh tìm kiếm:** Nền `bg-slate-100/80`, viền `focus:border-blue-500`, focus ring `focus:ring-4 focus:ring-blue-500/10`, bo tròn `rounded-full`.
- **Live Search Dropdown:** Khung lớn `rounded-3xl shadow-2xl`, hiển thị 2 cột (Từ khóa đề xuất bên trái, Lưới 4 sản phẩm liên quan bên phải).

### Cart Summary Widget
- **Khối dính (Sticky Card):** Nằm cố định khi cuộn trang (`sticky top-24`), nền trắng `rounded-3xl`, hiển thị tính tổng tiền real-time và nút thanh toán an toàn.

## Do's and Don'ts

### Do:
- **Do** giữ khoảng thở rộng rãi (whitespace) giữa các khối sản phẩm (`gap-6` đến `gap-8`).
- **Do** sử dụng định dạng tiền tệ chuẩn tiếng Việt (`xx,xxx,xxx đ` hoặc `VND`) và làm nổi bật giá bán với màu Crimson Rose.
- **Do** giữ cho các chuyển động micro-interaction luôn dưới 300ms với đường cong `ease-out` hoặc `ease-in-out` tự nhiên.
- **Do** dùng `Plus Jakarta Sans` với `font-black` cho tất cả các con số và tiêu đề quan trọng.

### Don't:
- **Don't** dùng viền đen đậm hoặc viền cứng `border-black` làm phá vỡ cảm giác mượt mà của Glassmorphism.
- **Don't** hiển thị các thông số rườm rà không cần thiết trên thẻ card xem nhanh (chỉ giữ lại 3 thông số vàng: Màn hình, RAM, ROM).
- **Don't** dùng các màu xanh đỏ nguyên bản chói gắt (`#ff0000`, `#0000ff`). Luôn dùng bảng màu HSL/Tailwind tinh tuyển (`#2563eb`, `#e11d48`).
