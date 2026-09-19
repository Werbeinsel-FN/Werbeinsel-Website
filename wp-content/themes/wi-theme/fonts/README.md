# Lokale Schriften

Solange in diesem Ordner keine Datei `fonts.css` liegt, lädt das Theme
Unbounded und Poppins weiterhin von `fonts.googleapis.com`. Das überträgt
die IP-Adresse jedes Besuchers an Google und ist in Deutschland
abmahnungsrelevant (LG München I, 3 O 17493/20).

## Umstellung in drei Schritten

1. Schriftdateien holen – zum Beispiel über <https://gwfh.mranftl.com/fonts>
   (früher google-webfonts-helper). Benötigt werden nur `woff2`:
   - Unbounded: 400, 700, 800
   - Poppins: 400, 500, 700, 800

2. Dateien in diesen Ordner legen und exakt so benennen:
   `unbounded-400.woff2`, `unbounded-700.woff2`, `unbounded-800.woff2`,
   `poppins-400.woff2`, `poppins-500.woff2`, `poppins-700.woff2`,
   `poppins-800.woff2`

3. `fonts.css.example` in `fonts.css` umbenennen.

Das Theme erkennt die Datei automatisch, bindet sie statt Google Fonts ein
und lässt auch den `preconnect`-Hinweis auf Google weg. Danach im Browser
unter Netzwerkanalyse prüfen, dass keine Anfrage mehr an `fonts.gstatic.com`
oder `fonts.googleapis.com` geht.
