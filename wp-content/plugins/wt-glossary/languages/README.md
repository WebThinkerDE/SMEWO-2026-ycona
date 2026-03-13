# Glossary Plugin – Translations

- **de_DE.po** – German (Germany) source translations for the text domain `wt-glossary`.

## Loading the translation

WordPress loads compiled `.mo` files from this folder. The expected file name is **de_DE.mo** (locale-based).

### Option 1: Compile with gettext (command line)

If you have gettext installed (e.g. on Linux/macOS or via Poedit):

```bash
msgfmt -o de_DE.mo de_DE.po
```

Put the generated `de_DE.mo` in this `languages` folder.

### Option 2: Loco Translate plugin

1. Install the [Loco Translate](https://wordpress.org/plugins/loco-translate/) plugin.
2. Go to **Loco Translate → Plugins → WebThinker Glossary**.
3. Add German (de_DE) and import or sync from `languages/de_DE.po`; Loco will create/update the `.mo` file.

### Option 3: Poedit

1. Open `de_DE.po` in [Poedit](https://poedit.net/).
2. Save; Poedit will create `de_DE.mo` in the same folder.

After `de_DE.mo` is present here, set the site language to **Deutsch** (or the user’s language to de_DE) and the plugin strings will appear in German.
