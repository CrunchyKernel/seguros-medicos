# RD Station Email Redesign — Style & Handoff Guide

**Project:** Seguro de Gastos Médicos Mayores — email campaign redesign
**Purpose:** Rebuild the ~17 legacy RD Station email campaigns into one consistent, modern, mobile-responsive style and re-import them into RD Station's new editor.
**Reference implementation:** `blogs/rd-station-mails/seguimiento-cotizador-01-redesign.html`
**Design language source:** the site's Porto `skin-insurance` theme and the brand accent used in the original welcome email.

---

## 1. Why we rebuild instead of edit

RD Station retired its old email editor. Any email or template built in the old editor is **locked** — it shows *"Esta plantilla no se puede editar… Las plantillas creadas con el antiguo editor de email no se pueden editar ni duplicar."* You can still view it and read its stats, but you cannot edit or duplicate it.

So the workflow is: **rebuild the design as standalone HTML → import it into the new editor via *Importar HTML* → swap it into the automation flow that used the old email.** Nothing is deleted; the new version runs on the same trigger and audience.

---

## 2. Design tokens

Everything below is already applied in the reference file. Keep these exact values so all campaigns match.

| Token | Value | Used for |
|---|---|---|
| Brand accent (primary) | `#4192AD` | Buttons, links, checkmarks, brand name |
| Page background | `#eaf5f8` | Area behind the white card |
| Card background | `#ffffff` | Main email container |
| Header gradient | `#ffffff` → `#c6e9f0` (fallback `#cdeef4`) | Logo band at top |
| Info / list card | `#f5f5f5` | Grouped lists, PD note |
| Feature / CTA card | `#ffffff` → `#d2f0f5` (fallback `#e6f6f9`) | Highlighted contact block |
| Heading text | `#21343a` | H1, bold emphasis |
| Body text | `#555555` | Paragraphs |
| Muted / footer text | `#9aa6aa` / `#7a8a8f` | Footer, fine print |
| Divider line | `#e4eef1` | Section separators |

**Typography:** `'Helvetica Neue', Helvetica, Arial, sans-serif` (email-safe stack).
Greeting H1 ≈ 27px / weight 700; body 16px / line-height 1.7; PD note 12px; footer 11px.

**Shape (corner radius):** main card `24px`; info & PD cards `16px`; feature card `20px`; images `15px`; **buttons = full pill** (see §3).

**Layout:** container max-width **600px**; side padding **44px** desktop, **24px** on mobile (via media query); one column only.

---

## 3. Buttons — the important part

Two styles only, both **full pills**, **regular weight (400)**, **no arrows/emojis** in the label.

- **Outline** (default CTA): transparent fill, `1px solid #4192AD` border, `#4192AD` text.
- **Solid** (single emphasis button, e.g. WhatsApp): `#4192AD` fill, `#ffffff` text.

### ⚠️ Critical gotcha: pill shape must live on the `<a>`, not the `<td>`

Email HTML uses `table { border-collapse: collapse; }`. With collapsed borders, **email clients ignore `border-radius` on a `<td>`** — the button renders as a square no matter how large the radius. Put the border, background, and `border-radius` on the **`<a>` element** (which is `display:inline-block`) and it rounds correctly.

**Outline pill:**
```html
<table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:0 auto;">
  <tr>
    <td align="center">
      <a href="URL" target="_blank" style="display:inline-block; padding:14px 30px;
         font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; font-size:17px; font-weight:400;
         line-height:1.2; color:#4192AD; text-decoration:none;
         border:1px solid #4192AD; border-radius:100px; background-color:transparent;">Label</a>
    </td>
  </tr>
</table>
```

**Solid pill:** same markup, but on the `<a>` use `color:#ffffff; background-color:#4192AD;` and drop the border.

`border-radius:100px` is intentionally larger than the button height — clients clamp it to half the height, giving perfect semicircle ends on any label length. (Note: Outlook/Windows renders square corners regardless — this is an accepted email limitation.)

---

## 4. Images

- **Always use `https://`.** The two content images originally used `http://`, which email clients and previews block. The site serves everything over `https://www.segurodegastosmedicosmayores.mx/...`, so use that host.
- **Best practice:** when importing into RD Station, re-upload each image so it's served from RD Station's own CDN (`d335luupugsy2.cloudfront.net`), like the logo already is. That's the most reliable path for deliverability.
- Every `<img>`: `display:block; height:auto;` a `width` attribute, a matching `max-width`, `border-radius:15px`, and real `alt` text.
- **Sizing:** full-width images use `width:100%`. For a half-width, centered image use `width:50%; max-width:256px; margin:0 auto;` (this is what the "¿Qué es deducible…?" article image uses).

---

## 5. RD Station placeholders — never touch these

When re-flowing content, keep these exact strings so RD Station can substitute them at send time:

- `*|PRIMER_NOMBRE|*` — recipient first name.
- `*|LINK-DE-COTIZACION|*` — the lead's quote link (appears inside quote URLs).
- `*UUID*` — used in RD Station's own links: view-as-web (`https://app.rdstation.email/mail/*UUID*`) and unsubscribe (`https://app.rdstation.email/descadastrar/*UUID*`).

Every email must keep a footer with the **sender name, physical address, and an unsubscribe link** (legal requirement + RD Station requirement).

---

## 6. Email-safe HTML rules

- Layout with **tables**, not `div`/flex/grid. **All CSS inline** (plus one `<style>` block for the media query and resets).
- No Bootstrap, no external stylesheets, no JavaScript.
- Keep the `<!--[if mso]>` "ghost table" wrapper around the 600px card for Outlook.
- Keep the `@media (max-width:620px)` block that makes the card full-width and reduces side padding.
- Test in a real browser and at least one email client (Gmail + Apple Mail cover most of the audience) before shipping.

---

## 7. Master template skeleton

Copy `seguimiento-cotizador-01-redesign.html` as the starting point for each campaign. Its zones, top to bottom:

1. Hidden preheader (inbox preview text)
2. Header band — logo (centered, ~240px)
3. Greeting `Hola *|PRIMER_NOMBRE|*,`
4. Body paragraphs
5. Grey info card for any bulleted list (blue `✓` marks)
6. CTA (outline pill)
7. Image(s)
8. Divider
9. Feature card (pale-cyan gradient) for the key contact CTA + WhatsApp
10. Closing text + signature (name bold `#21343a`, site name `#4192AD`)
11. Grey PD note card
12. Footer (sender, address, unsubscribe)

---

## 8. Step-by-step: redesigning one campaign

1. **Get the source.** Export or copy the old email's HTML/content from RD Station (or the automation flow that sends it).
2. **Duplicate the template** file and rename it for the campaign.
3. **Flow the content in**, zone by zone. Preserve **every** paragraph, list item, button, image, and merge tag — do not drop copy.
4. **Style the buttons** as outline pills (solid only for the one emphasis button). Remove arrows.
5. **Fix image URLs** to `https://www…` and set widths.
6. **Verify nothing was lost** — compare button labels, links, and merge tags against the original (see checklist).
7. **Render-test** in a browser and an email client.
8. **Import to RD Station:** new email → *Importar HTML* → paste → save.
9. **Wire it up:** open the automation flow (Automatización) that used the old email, pause it, point the email step at the new email, reactivate. The trigger and audience stay the same.
10. **Send a test** to yourself and confirm it renders and the links work.

---

## 9. QA checklist (per email)

- [ ] All original copy present (intro, lists, closing, PD, footer)
- [ ] All buttons present, correct labels, correct links
- [ ] Merge tags intact: `*|PRIMER_NOMBRE|*`, `*|LINK-DE-COTIZACION|*`, `*UUID*`
- [ ] Buttons are pills (radius on the `<a>`), regular weight, no arrows
- [ ] Brand color is `#4192AD` everywhere (no stray bright blue)
- [ ] All images `https://`, sized, `border-radius:15px`, with `alt`
- [ ] Footer has sender + address + working unsubscribe
- [ ] Renders correctly on desktop and mobile widths
- [ ] Test send received and links verified
