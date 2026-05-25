# UA FMS — Organization Staff Android App

Native WebView wrapper for the **org staff** portal only. It mirrors `launchers/org_launcher.py`:

1. Local gate screen — user enters the org access key (`FMS_ORG_SECRET`).
2. WebView loads `{FMS_LOGIN_URL}?access_token={FMS_ACCESS_TOKEN}&role=org_staff`.
3. User logs in with Laravel credentials (same as desktop).

Values are set at **APK build time** in `app/build.gradle.kts` (same idea as baking env vars into a PyInstaller `.exe`).

## Prerequisites

- [Android Studio](https://developer.android.com/studio) (Ladybug or newer recommended)
- JDK 17 (bundled with Android Studio)
- Laravel backend reachable from the phone (LAN IP or HTTPS domain)

## Configure before building

Edit `app/build.gradle.kts` → `defaultConfig` → `buildConfigField` lines:

```kotlin
buildConfigField("String", "FMS_LOGIN_URL", "\"https://your-server.edu.ph/fms-portal-entry\"")
buildConfigField("String", "FMS_ACCESS_TOKEN", "\"your-access-token\"")
buildConfigField("String", "FMS_ORG_SECRET", "\"your-org-secret\"")
```

These must match your Laravel `.env`:

- `FMS_LOGIN_URL`
- `FMS_ACCESS_TOKEN`
- `FMS_ORG_SECRET`

For **LAN testing** on a physical device, use your PC’s LAN address (not `127.0.0.1`), e.g. `http://192.168.0.10:8000/fms-portal-entry`. Cleartext HTTP is allowed in debug builds only.

## Build APK (Android Studio)

1. Open Android Studio → **Open** → select folder `android/ua-fms-org`.
2. Wait for Gradle sync to finish.
3. **Build → Build Bundle(s) / APK(s) → Build APK(s)**.
4. APK output:
   - Debug: `app/build/outputs/apk/debug/app-debug.apk`
   - Release: `app/build/outputs/apk/release/app-release-unsigned.apk` (sign before distributing)

## Build APK (command line)

From `android/ua-fms-org` (after Android Studio has synced once, or after `gradle wrapper` exists):

```bash
# Windows
gradlew.bat assembleDebug

# macOS / Linux
./gradlew assembleDebug
```

Release (requires signing config in `app/build.gradle.kts`):

```bash
gradlew.bat assembleRelease
```

## Install on a device

```bash
adb install -r app/build/outputs/apk/debug/app-debug.apk
```

Or copy the APK to the phone and install (enable “Install unknown apps” if needed).

## Why not package `org_launcher.py` directly?

`org_launcher.py` targets **Windows** (pywebview + PyInstaller). Android needs either:

| Approach | Notes |
|----------|--------|
| **This project (recommended)** | Small Kotlin + WebView APK, fast, matches org launcher behavior |
| **pywebview + Buildozer** | Packages Python on Android; large APK, complex toolchain, rarely worth it for a URL + key gate |

Admin and college launchers stay desktop-only unless you duplicate this app with different `FMS_*` constants and labels.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Blank WebView / cannot connect | Use LAN IP or HTTPS; phone must reach the server |
| 404 on portal | `FMS_ACCESS_TOKEN` or URL mismatch with Laravel `.env` |
| Invalid key at gate | `FMS_ORG_SECRET` in APK must match `.env` |
| HTTP blocked on release build | Use HTTPS, or test with **debug** APK (cleartext allowed) |

## Related docs

- `LAUNCHER_README.md` — desktop org launcher
- `DEPLOYMENT_GUIDE.md` — production tokens and Laravel deploy
- `TEST_ACCOUNTS.md` — create `org@example.com` via `/make-org-staff` before testing login
