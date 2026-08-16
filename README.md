# Formula Paddock WordPress theme

Tema WordPress personalizzato attivo su `formulapaddock.it`.

## Pubblicazione dal computer

Aprire PowerShell nella cartella del repository ed eseguire:

```powershell
.\tools\deploy-theme.ps1
```

Il comando legge le credenziali esclusivamente da `F:\seo\conn.php` e pubblica
il tema nella cartella `wp-content/themes/f1-speed-seo` del sito. Le credenziali
non vengono salvate nel repository.

Per controllare in anticipo quali file saranno caricati:

```powershell
.\tools\deploy-theme.ps1 -WhatIf
```

