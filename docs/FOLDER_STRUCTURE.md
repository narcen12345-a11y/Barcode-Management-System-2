# Folder Structure

## Struktur Backend
- app/Actions: menampung action atau use case.
- app/DTOs: tempat DTO untuk transfer data antar layer.
- app/Enums: definisi enum domain.
- app/Exceptions: exception kustom.
- app/Helpers: helper reusable.
- app/Interfaces: kontrak interface.
- app/Policies: kebijakan akses.
- app/Repositories: repository abstraction.
- app/Rules: custom validation rules.
- app/Services: service layer untuk logika domain.
- app/Traits: trait reusable.
- app/ValueObjects: objek nilai domain.
- app/Support: komponen pendukung.
- app/Observers: observer model.
- app/Events: event domain.
- app/Listeners: listener event.
- app/Jobs: background job.
- app/Notifications: class notifikasi.
- app/Mail: class email.
- app/Providers: provider Laravel.

## Fungsi setiap folder
Setiap folder memiliki tujuan yang jelas agar kode dapat dipisahkan berdasarkan tanggung jawab dan memudahkan maintenance.

## Alur data dari Request sampai Response
1. Request masuk melalui controller atau route.
2. Controller memanggil service.
3. Service berinteraksi dengan repository atau komponen lain.
4. Data diproses dan dikirimkan melalui DTO atau value object jika diperlukan.
5. Response dikembalikan ke client dengan format konsisten.
