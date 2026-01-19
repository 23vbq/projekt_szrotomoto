# Sprawozdanie z Projektu - Szrotomoto

## 1. Cel Projektu i MVP

**Cel projektu:** stworzenie platformy internetowej do zarządzania ofertami sprzedaży pojazdów używanych. Aplikacja ma umożliwiać użytkownikom łatwe publikowanie ofert swoich pojazdów oraz przeglądanie i wyszukiwanie ofert innych użytkowników. Głównym założeniem jest stworzenie prostego, ale funkcjonalnego systemu, który pozwoli na efektywną wymianę informacji między kupującymi a sprzedającymi pojazdy.

**MVP (Minimum Viable Product)** projektu obejmuje następujące funkcjonalności:
- System rejestracji i logowania użytkowników
- Tworzenie, edycja i usuwanie ofert pojazdów z pełnymi danymi technicznymi
- Przeglądanie listy aktywnych ofert z możliwością wyszukiwania i filtrowania
- Wyświetlanie szczegółów pojedynczej oferty
- Upload i wyświetlanie zdjęć pojazdów
- Zarządzanie statusami ofert (aktywna, sprzedana, usunięta)
- Katalog marek i modeli pojazdów do wyboru przy tworzeniu oferty

MVP skupia się na podstawowych funkcjach niezbędnych do działania platformy, bez zaawansowanych funkcji takich jak powiadomienia, system ocen czy integracje z zewnętrznymi serwisami, które mogą być dodane w przyszłości.

## 2. Wprowadzenie

Projekt **Szrotomoto** jest aplikacją webową służącą do zarządzania ofertami sprzedaży pojazdów. System umożliwia użytkownikom przeglądanie, tworzenie, edycję oraz zarządzanie ofertami samochodów. Aplikacja została zaprojektowana z myślą o użytkownikach pragnących kupować lub sprzedawać pojazdy, oferując intuicyjny interfejs oraz kompleksowe zarządzanie danymi technicznymi pojazdów.

Backend został zaprojektowany jako API oparte na PHP, które komunikuje się z frontendem poprzez standardowe żądania HTTP. API jest inspirowane zasadami REST, ale używa prostszej struktury z plikami PHP i parametrami query string. Decyzja o zastosowaniu architektury API pozwala na elastyczność w implementacji frontendu oraz umożliwia wykorzystanie tego samego backendu przez różne aplikacje klienckie (web, mobilne, desktopowe). API zostało zbudowane z naciskiem na czytelność, bezpieczeństwo oraz łatwość utrzymania, co jest kluczowe dla długoterminowego rozwoju projektu.

## 3. Architektura i Stack Technologiczny

### 3.1 Technologie

Backend został zaimplementowany w **PHP 8.4**, co zapewnia dostęp do najnowszych funkcjonalności języka oraz optymalizacji wydajnościowych. Wybór PHP jako technologii głównej podyktowany był jego powszechnym zastosowaniem w aplikacjach webowych, bogatym ekosystemem oraz łatwością wdrożenia.

Jako serwer webowy wykorzystano **Apache HTTP Server**, który zapewnia stabilność i szerokie wsparcie dla aplikacji PHP. Apache został skonfigurowany z modułem `mod_rewrite`, umożliwiającym przyjazne adresy URL oraz przekierowania. **MySQL 8.4** został wybrany jako system zarządzania bazą danych ze względu na jego niezawodność, wydajność oraz szerokie wsparcie w środowisku PHP. Komunikacja z bazą danych odbywa się poprzez **PDO** (PHP Data Objects), które zapewnia bezpieczne, parametryzowane zapytania chroniące przed atakami SQL injection.

Całość została skonteneryzowana przy użyciu **Docker**, co umożliwia łatwe zarządzanie środowiskiem deweloperskim oraz zapewnia spójność między różnymi maszynami. Docker Compose koordynuje działanie wielu kontenerów (backend, frontend, baza danych), zapewniając izolację oraz kontrolę nad sieciami i wolumenami.

### 3.2 Architektura Systemu

Aplikacja została zaprojektowana w architekturze **warstwowej** z wyraźnym podziałem odpowiedzialności. Taki podział zapewnia modularność systemu oraz ułatwia jego rozwój i utrzymanie. Każda warstwa ma jasno określone zadania i odpowiedzialności, co minimalizuje zależności między komponentami.

**Warstwa API** (`backend/api/`) zawiera endpointy obsługujące żądania HTTP. Każdy endpoint jest osobnym plikiem PHP, który odpowiada za obsługę konkretnego zasobu lub operacji. Endpointy są zorganizowane w logiczne katalogi odpowiadające domenom biznesowym (np. `login/`, `offers/`, `vehicles/`), co ułatwia nawigację po kodzie.

**Warstwa logiki biznesowej** (`backend/utils/`) zawiera klasy pomocnicze i narzędzia używane przez endpointy. Te klasy grupują wspólną logikę, żeby nie powtarzać kodu i zachować spójność. W skład tej warstwy wchodzą: `Database` (zarządzanie połączeniami), `QueryBuilder` (budowanie zapytań SQL), `Response` (formatowanie odpowiedzi), `Session` (zarządzanie sesjami), `Env` (odczyt konfiguracji), `Consts` (wartości stałe), `AttachmentUploader` (upload plików) oraz `ArrayUtils` (pomocnicze funkcje do operacji na tablicach, np. mapowanie po kolumnie).

**Warstwa dostępu do danych** wykorzystuje klasy `Database` oraz `QueryBuilder`. Klasa `Database` zarządza połączeniami z bazą danych, implementując wzorzec singleton, żeby nie tworzyć wielu połączeń. `QueryBuilder` pozwala na budowanie zapytań SQL w czytelny sposób, co ułatwia pracę z bazą danych.

**Warstwa prezentacji** to klasa `Response`, która formatuje odpowiedzi API w jednolity sposób. Wszystkie odpowiedzi są zwracane w formacie JSON z odpowiednimi kodami statusu HTTP, co zapewnia spójność interfejsu API. Dodatkowo, plik `utils_loader.php` inicjalizuje system obsługi błędów i konfiguruje nagłówki CORS (Cross-Origin Resource Sharing), umożliwiając komunikację między frontendem a backendem działającymi na różnych portach. Exception handler i error handler zapewniają spójne formatowanie błędów i logowanie wyjątków dla celów debugowania.

Komunikacja między warstwami odbywa się przez jasno zdefiniowane interfejsy, co zapewnia separację odpowiedzialności i ułatwia testowanie oraz utrzymanie kodu. Dzięki takiej architekturze można łatwo wymieniać implementacje poszczególnych warstw bez wpływu na pozostałe komponenty systemu.

## 4. Projekt Bazy Danych

### 4.1 Schemat Relacyjny

Baza danych została zaprojektowana zgodnie z zasadami normalizacji, co zapewnia efektywne przechowywanie danych i minimalizuje redundancję. Projekt schematu uwzględnia relacje między encjami i zapewnia integralność referencyjną przez użycie kluczy obcych.

Tabela **users** przechowuje dane użytkowników systemu, w tym unikalny adres email, zahashowane hasło oraz imię użytkownika. Email jest unikalny w całej bazie, co zapobiega duplikatom kont. Hasła są przechowywane jako hashe generowane przez funkcję `password_hash()` PHP, co zapewnia bezpieczeństwo wrażliwych danych.

Tabela **brands** zawiera katalog marek pojazdów dostępnych w systemie. Każda marka ma unikalną nazwę oraz timestamp utworzenia, co umożliwia śledzenie historii dodawania marek.

Tabela **models** przechowuje modele pojazdów powiązane z markami poprzez relację wiele-do-jednego. Każdy model musi być przypisany do istniejącej marki, co zapewnia spójność danych. Kombinacja `brand_id` i `name` jest unikalna, co zapobiega duplikatom modeli w ramach jednej marki.

Tabela **offers** jest centralną tabelą systemu, przechowującą oferty pojazdów wraz z pełnymi danymi technicznymi. Zawiera ona ponad 20 pól opisujących pojazd, w tym dane podstawowe (cena, rok produkcji, przebieg), parametry techniczne (moc, moment obrotowy, pojemność), oraz informacje o stanie pojazdu (historia wypadków, gwarancja, książka serwisowa).

Tabela **attachments** przechowuje metadane załączników (zdjęcia pojazdów), w tym unikalną nazwę pliku oraz typ MIME. Pliki są fizycznie przechowywane w systemie plików, a baza danych zawiera tylko referencje do nich.

Tabela **migrations** służy do śledzenia wykonanych migracji bazy danych, co pozwala kontrolować wersje schematu i zapewnia, że każda migracja zostanie wykonana tylko raz.

#### Diagram Relacji Bazy Danych

```
┌─────────────┐
│   brands    │
│─────────────│
│ • id (PK)   │
│ • name      │
└──────┬──────┘
       │
       │ 1:N
       │ (ON DELETE CASCADE)
       │
       ▼
┌─────────────┐         ┌─────────────┐
│   models    │         │    users    │
│─────────────│         │─────────────│
│ • id (PK)   │         │ • id (PK)   │
│ • brand_id  │◄────────┤ • email     │
│   (FK)      │         │ • password_ │
│ • name      │         │   hash      │
└──────┬──────┘         │ • name      │
       │                └──────┬──────┘
       │                       │
       │ 1:N                   │ 1:N
       │                       │
       └───────────┬───────────┘
                   │
                   ▼
         ┌──────────────────┐
         │     offers       │
         │──────────────────│
         │ • id (PK)        │
         │ • model_id (FK)  │◄───┐
         │ • created_by(FK) │◄───┘
         │ • status         │
         │ • title, price,  │
         │   vin, ...       │
         │ • attachments    │
         │   (JSON)         │
         └────────┬─────────┘
                  │
                  │ (logiczna relacja przez JSON)
                  │
                  ▼
         ┌─────────────┐
         │ attachments │
         │─────────────│
         │ • id (PK)   │
         │ • filename  │
         │ • mime_type │
         └─────────────┘

Legenda:
• PK = Primary Key (klucz główny)
• FK = Foreign Key (klucz obcy)
1:N = Relacja jeden-do-wielu (jedna marka → wiele modeli)

Uwagi:
- offers.model_id → models.id (FK bez ON DELETE CASCADE)
- offers.created_by → users.id (FK bez ON DELETE CASCADE)
- models.brand_id → brands.id (FK z ON DELETE CASCADE)
- offers.attachments przechowuje tablicę ID załączników w JSON
  (relacja logiczna, nie fizyczny klucz obcy)
```

### 4.2 Kluczowe Decyzje Projektowe

**Integralność referencyjna** jest zapewniona przez użycie kluczy obcych. Relacja `models.brand_id → brands.id` wykorzystuje opcję `ON DELETE CASCADE`, co oznacza, że usunięcie marki automatycznie usuwa wszystkie powiązane modele. Relacje `offers.model_id → models.id` oraz `offers.created_by → users.id` nie wykorzystują `ON DELETE CASCADE`, więc usunięcie modelu lub użytkownika nie powoduje automatycznego usunięcia ofert. Taka strategia została wybrana, aby zachować historię ofert nawet po usunięciu modelu lub użytkownika, co może być przydatne do celów archiwalnych lub statystycznych. System zapobiega jednak powstawaniu "sierocych" rekordów poprzez walidację na poziomie aplikacji - nie można utworzyć oferty z nieistniejącym modelem lub użytkownikiem.

**Unikalność VIN** jest sprawdzana zarówno w bazie danych, jak i w aplikacji. Numer VIN (Vehicle Identification Number) jest unikalnym identyfikatorem pojazdu, więc jego duplikacja w systemie byłaby błędem logicznym. Przed utworzeniem oferty system sprawdza, czy VIN już istnieje w bazie, zwracając odpowiedni kod błędu (409 Conflict) w przypadku próby duplikacji. Ta walidacja zapobiega przypadkowemu lub celowemu tworzeniu duplikatów ofert dla tego samego pojazdu.

**JSON dla załączników** - tabela `offers` przechowuje tablicę ID załączników w formacie JSON w kolumnie `attachments`. Ta decyzja pozwala elastycznie zarządzać wieloma zdjęciami dla jednej oferty bez konieczności tworzenia dodatkowej tabeli relacyjnej. JSON pozwala łatwo dodawać i usuwać załączniki, a także zachować kolejność zdjęć. Alternatywne rozwiązanie z osobną tabelą relacyjną byłoby bardziej normalizowane, ale wymagałoby dodatkowych zapytań JOIN przy pobieraniu ofert.

**Statusy ofert** to system do zarządzania cyklem życia oferty. Oferta może mieć status `active` (aktywna, widoczna dla wszystkich), `sold` (sprzedana, widoczna ale oznaczona jako niedostępna) lub `removed` (usunięta, widoczna tylko dla twórcy). System statusów pozwala użytkownikom zarządzać swoimi ofertami bez konieczności ich trwałego usuwania, co może być przydatne do celów historycznych lub statystycznych. Logika biznesowa sprawia, że tylko aktywne oferty są widoczne w wynikach wyszukiwania, podczas gdy nieaktywne oferty są dostępne tylko dla ich twórców.

### 4.3 Migracje

System migracji bazy danych (`backend/bin/migrate.php`) pozwala na wersjonowanie zmian w schemacie i kontrolowane wdrażanie aktualizacji struktury bazy danych. Każda migracja jest plikiem SQL z timestampem w nazwie (format: `YYYYMMDDHHMM.sql`), co pozwala na automatyczne sortowanie migracji chronologicznie oraz śledzenie historii zmian.

Skrypt migracyjny odczytuje wszystkie pliki SQL z katalogu `migrations/`, porównuje je z listą wykonanych migracji w tabeli `migrations`, a następnie wykonuje tylko te migracje, które jeszcze nie zostały zastosowane. Dzięki temu każda migracja zostanie wykonana dokładnie raz, nawet jeśli skrypt zostanie uruchomiony wielokrotnie. System migracji jest ważny dla współpracy zespołowej i wdrażania aplikacji na różnych środowiskach (deweloperskie, testowe, produkcyjne), zapewniając spójność schematu bazy danych we wszystkich środowiskach.

## 5. Projekt API

### 5.1 Architektura API

API zostało zaprojektowane w stylu inspirowanym REST (Representational State Transfer), co zapewnia spójność i łatwość w użyciu. API wykorzystuje niektóre zasady REST, ale nie jest w pełni RESTful - endpointy są zorganizowane jako pliki PHP z parametrami w query string, co jest prostsze w implementacji bez frameworka.

**Organizacja endpointów** opiera się na logicznym grupowaniu w katalogach odpowiadających domenom biznesowym. Na przykład `/api/offers/search.php` zwraca listę ofert, a `/api/offers/show.php?offer_id=123` zwraca konkretną ofertę. Taka organizacja jest intuicyjna i łatwa do zrozumienia, choć nie jest to pełna implementacja REST (w pełnym REST byłoby `/api/offers/123`). Endpointy są łatwe do użycia zarówno dla deweloperów, jak i narzędzi jak Postman czy curl.

**Metody HTTP** są używane do określenia operacji. Metoda GET jest używana do odczytu danych (np. pobieranie listy ofert, szczegółów oferty), podczas gdy POST jest wykorzystywana do tworzenia nowych zasobów (rejestracja, tworzenie oferty) oraz modyfikacji istniejących (edycja oferty). Taki podział metod sprawia, że operacje są jasne i pozwala na optymalizację na poziomie serwera (np. cache dla żądań GET). API nie wykorzystuje metod PUT i DELETE, co jest typowe dla prostszych implementacji bez frameworka.

**Kody statusu HTTP** informują o wyniku operacji, co jest ważne dla poprawnej obsługi odpowiedzi po stronie klienta. Kod 200 oznacza sukces, 201 oznacza utworzenie nowego zasobu, 400 sygnalizuje błąd walidacji, 401 oznacza brak autentykacji, 403 wskazuje na brak uprawnień, 404 oznacza brak zasobu, 409 sygnalizuje konflikt (np. duplikat), a 500 oznacza błąd serwera.

**Format JSON** jest używany dla wszystkich odpowiedzi, co zapewnia spójność oraz łatwość parsowania po stronie klienta. JSON jest również czytelny dla ludzi, co ułatwia debugowanie i rozwój aplikacji.

### 5.2 Struktura Endpointów

API zostało zorganizowane w logiczne grupy odpowiadające domenom biznesowym aplikacji. Taka organizacja ułatwia nawigację po kodzie, utrzymanie i rozwijanie funkcjonalności. Każda grupa endpointów jest w osobnym katalogu, co zapewnia czytelną strukturę projektu.

**Autentykacja** (`/api/login/`) zawiera endpointy odpowiedzialne za zarządzanie sesją użytkownika. `login.php` obsługuje proces logowania, weryfikując dane użytkownika i tworząc sesję. `register.php` pozwala rejestrować nowych użytkowników z walidacją danych wejściowych i sprawdzaniem unikalności emaila. `logout.php` kończy sesję użytkownika, niszcząc dane sesyjne. `me.php` pozwala sprawdzić aktualny status autentykacji, co jest wykorzystywane przez frontend do dynamicznego dostosowywania interfejsu użytkownika.

**Oferty** (`/api/offers/`) to największa grupa endpointów, odpowiadająca za zarządzanie ofertami pojazdów. 

`search.php` zwraca listę wszystkich aktywnych ofert z pełnymi danymi, włączając informacje o marce, modelu oraz użytkowniku. Endpoint obsługuje zaawansowane filtrowanie po wielu kryteriach (marka, model, typ paliwa, skrzynia biegów, typ nadwozia, zakres ceny, rok produkcji, przebieg) oraz wyszukiwanie tekstowe w tytule, opisie, marce, modelu, VIN i numerze rejestracyjnym. Dodatkowo, endpoint pozwala sortować po różnych polach (cena, przebieg, rok produkcji, data utworzenia) w kierunku rosnącym lub malejącym.

`show.php` pobiera szczegóły pojedynczej oferty. Nieaktywne oferty są widoczne tylko dla ich twórców. Endpoint zwraca również flagę `isCurrentUserOwner`, która informuje, czy zalogowany użytkownik jest właścicielem oferty.

`create.php` obsługuje tworzenie nowych ofert z walidacją danych oraz możliwością jednoczesnego uploadu wielu zdjęć (`files[]`).

`edit.php` umożliwia modyfikację istniejących ofert z weryfikacją uprawnień. Tylko przesłane pola są aktualizowane. Endpoint obsługuje też dodawanie nowych zdjęć do istniejących załączników.

`setAsSold.php` i `setAsRemoved.php` pozwalają na zmianę statusu oferty, co jest prostsze niż pełna edycja. Oba endpointy zwracają zaktualizowaną ofertę z pełnymi danymi.

**Pojazdy** (`/api/vehicles/`) dostarcza danych referencyjnych o pojazdach. `brands.php` zwraca listę wszystkich dostępnych marek, posortowaną alfabetycznie. `models.php` zwraca listę modeli, z opcjonalnym filtrem po marce (`brand_id`), co pozwala dynamicznie ładować modele w interfejsie użytkownika w zależności od wybranej marki.

**Wartości statyczne** (`/api/values/`) zawiera endpointy zwracające listy wartości używanych w formularzach, takie jak typy paliw, typy skrzyń biegów, typy nadwozia oraz lista krajów. Te endpointy są wykorzystywane do dynamicznego generowania pól formularzy i walidacji danych wejściowych.

**Załączniki** (`/api/attachments/`) zarządza plikami graficznymi. 

`create.php` obsługuje upload pojedynczego zdjęcia (pole `file`) z walidacją typu i rozmiaru pliku, zwracając ID załącznika, który może być następnie powiązany z ofertą. Warto zauważyć, że endpoint `attachments/create.php` przyjmuje pojedynczy plik (`file`), podczas gdy endpointy `offers/create.php` i `offers/edit.php` obsługują wiele plików jednocześnie (`files[]`).

`show.php` służy do wyświetlania zdjęć, zwracając plik z odpowiednim nagłówkiem Content-Type oraz Content-Length, co umożliwia bezpośrednie wyświetlanie obrazów w przeglądarce. Endpoint weryfikuje istnienie pliku zarówno w bazie danych, jak i w systemie plików, co zapewnia spójność danych.

**Status** (`/api/status.php`) to endpoint diagnostyczny zwracający status serwera oraz aktualny czas z serwera i bazy danych, co pozwala sprawdzić połączenie z bazą danych i synchronizację czasu. Endpoint główny (`/index.php`) zwraca podstawowy status "ok", służąc jako healthcheck dla całego backendu.

### 5.3 Walidacja i Obsługa Błędów

Każdy endpoint ma wielowarstwową walidację danych wejściowych, co jest ważne dla bezpieczeństwa i niezawodności aplikacji. Walidacja działa na kilku poziomach - od podstawowych sprawdzeń do złożonych reguł biznesowych.

**Sprawdzanie wymaganych pól** jest pierwszym krokiem walidacji. Endpointy sprawdzają, czy wszystkie pola oznaczone jako wymagane zostały przesłane w żądaniu. W przypadku braku wymaganych pól, system zwraca błąd 400 Bad Request z komunikatem wskazującym brakujące pola. Ta walidacja zapobiega przetwarzaniu niekompletnych danych i zapewnia, że wszystkie krytyczne informacje są dostępne.

**Walidacja formatów** sprawdza poprawność formatów danych, takich jak email i VIN (Vehicle Identification Number). Email jest walidowany przy użyciu wbudowanych funkcji PHP, co zapewnia zgodność ze standardami RFC. VIN jest sprawdzany pod kątem unikalności i poprawności formatu, co zapobiega wprowadzaniu błędnych danych.

**Weryfikacja wartości enum** sprawdza, czy wartości takie jak typ paliwa, typ skrzyni biegów czy typ nadwozia są na dozwolonej liście. Listy te są przechowywane w klasie `Consts`, co zapewnia spójność i ułatwia aktualizację dostępnych opcji. Próba użycia nieprawidłowej wartości skutkuje błędem 400 Bad Request.

**Sprawdzanie unikalności** jest wykonywane dla pól, które muszą być unikalne w systemie, takich jak email użytkownika i VIN pojazdu. Przed utworzeniem nowego rekordu system wykonuje zapytanie do bazy danych, sprawdzając, czy wartość już istnieje. W przypadku duplikatu zwracany jest błąd 409 Conflict, co jest odpowiednim kodem dla takiej sytuacji.

**Autoryzacja** jest sprawdzana na poziomie endpointów wymagających uprawnień. System sprawdza nie tylko, czy użytkownik jest zalogowany, ale też czy ma uprawnienia do wykonania danej operacji. Na przykład, tylko właściciel oferty może ją edytować lub zmienić jej status. Próba wykonania nieautoryzowanej operacji skutkuje błędem 403 Forbidden.

Błędy są zwracane w spójnym formacie JSON z odpowiednimi kodami HTTP, co ułatwia obsługę po stronie klienta. Każdy błąd zawiera pole `message` z opisem problemu, co pozwala aplikacji klienckiej wyświetlić użytkownikowi zrozumiały komunikat. Jednolity format odpowiedzi błędów ułatwia też debugowanie i integrację z różnymi klientami API.

## 6. Bezpieczeństwo i Autentykacja

### 6.1 System Sesji

Autentykacja opiera się na **sesjach PHP** z wykorzystaniem cookies, co jest standardowym i bezpiecznym rozwiązaniem dla aplikacji webowych. Sesje PHP są zarządzane przez serwer, a identyfikator sesji jest przechowywany w cookie po stronie klienta. Taki mechanizm zapewnia, że dane sesyjne są przechowywane bezpiecznie po stronie serwera, a klient otrzymuje jedynie identyfikator sesji.

Klasa `Session` to warstwa nad standardowymi funkcjami sesji PHP, która centralnie zarządza sesjami i zapewnia spójne API w całej aplikacji. Klasa implementuje wzorzec Singleton, więc sesja jest inicjalizowana tylko raz na żądanie. Metoda `start()` sprawdza status sesji i inicjalizuje ją tylko wtedy, gdy nie została jeszcze uruchomiona, co zapobiega konfliktom oraz zapewnia wydajność.

Sesje są skonfigurowane z parametrami cookie dostosowanymi do komunikacji cross-origin: `samesite` ustawione na `Lax` (pozwala na cross-origin requests, ale zachowuje ochronę CSRF), `httponly` ustawione na `true` (zapobiega dostępowi JavaScript do cookie), `secure` ustawione na `false` (dla środowiska deweloperskiego, w produkcji z HTTPS powinno być `true`), oraz `lifetime` ustawione na `0` (sesja wygasa po zamknięciu przeglądarki).

Metody kontroli dostępu `allowAuthenticatedOnly()` oraz `allowUnauthenticatedOnly()` pozwalają na łatwą kontrolę dostępu w endpointach. `allowAuthenticatedOnly()` sprawdza, czy użytkownik jest zalogowany, i zwraca błąd 401 Unauthorized w przeciwnym przypadku. `allowUnauthenticatedOnly()` zapobiega dostępowi do endpointów (np. rejestracja, logowanie) dla już zalogowanych użytkowników, zwracając błąd 400 Bad Request. Dzięki tym metodom nie trzeba powtarzać kodu sprawdzającego autentykację w każdym endpoincie.

Dane użytkownika są przechowywane w sesji w bezpieczny sposób. Po udanym logowaniu, system zapisuje w sesji flagę `is_authenticated`, identyfikator użytkownika (`user_id`) oraz imię użytkownika (`user_name`). Te dane są wykorzystywane przez endpointy do identyfikacji użytkownika i do wyświetlania informacji w interfejsie. Metoda `logout()` czyści wszystkie dane sesyjne i niszczy sesję, zapewniając bezpieczne wylogowanie użytkownika.

### 6.2 Hasła

Hasła użytkowników są przechowywane jako hashe z wykorzystaniem funkcji `password_hash()` PHP z algorytmem `PASSWORD_BCRYPT`. Algorytm bcrypt to bezpieczne, sprawdzone rozwiązanie do hashowania haseł, które automatycznie generuje sól (salt) dla każdego hasła, więc nawet identyczne hasła będą miały różne hashe. Bcrypt jest algorytmem adaptacyjnym, co oznacza, że koszt obliczeniowy może być dostosowany do zwiększenia bezpieczeństwa wraz z rozwojem mocy obliczeniowej.

Podczas rejestracji, hasło użytkownika jest hashowane przed zapisaniem do bazy danych. Podczas logowania, system wykorzystuje funkcję `password_verify()` do porównania wprowadzonego hasła z hashem przechowywanym w bazie danych. Funkcja ta jest odporna na ataki timingowe, co jest ważne dla bezpieczeństwa. Hashe są przechowywane w kolumnie `password_hash` w tabeli `users`, a oryginalne hasła nigdy nie są przechowywane ani logowane, co jest zgodne z najlepszymi praktykami bezpieczeństwa.

System wymaga również, aby użytkownik potwierdził hasło podczas rejestracji (pole `repeated_password`), co zmniejsza ryzyko błędów w wprowadzaniu hasła. Walidacja sprawdza, czy oba hasła są identyczne przed zapisaniem użytkownika do bazy danych.

### 6.3 Autoryzacja

System sprawdza dostęp na poziomie endpointów, więc użytkownicy mogą wykonywać tylko te operacje, do których mają uprawnienia. Autoryzacja jest sprawdzana po autentykacji - najpierw system sprawdza, czy użytkownik jest zalogowany, a potem czy ma odpowiednie uprawnienia.

**Własność oferty** jest sprawdzana przed każdą operacją modyfikującą ofertę (edycja, zmiana statusu, usunięcie). System pobiera ofertę z bazy danych i porównuje pole `created_by` z identyfikatorem zalogowanego użytkownika. Jeśli użytkownik nie jest właścicielem oferty, operacja jest odrzucana z błędem 403 Forbidden. Ta kontrola jest ważna dla bezpieczeństwa, bo zapobiega nieautoryzowanym modyfikacjom ofert innych użytkowników.

**Widoczność nieaktywnych ofert** jest kontrolowana przez logikę biznesową w endpoincie `show.php`. Gdy oferta ma status inny niż `active`, system sprawdza, czy żądanie pochodzi od właściciela oferty. Jeśli nie, zwracany jest błąd 404 Not Found, co ukrywa istnienie nieaktywnych ofert przed nieuprawnionymi użytkownikami. Dzięki temu użytkownicy mają prywatność i mogą zarządzać historią swoich ofert.

**Upload plików** wymaga autentykacji, co zapobiega nieautoryzowanemu wykorzystaniu zasobów serwera i chroni przed spamem. Endpoint `attachments/create.php` używa metody `Session::allowAuthenticatedOnly()` do wymuszenia autentykacji przed przetworzeniem uploadu. Dodatkowo, każdy załącznik jest powiązany z użytkownikiem, co pozwala śledzić pochodzenie plików i zarządzać przestrzenią dyskową per użytkownik.

### 6.4 Upload Plików

Klasa `AttachmentUploader` obsługuje bezpieczny upload plików z wieloma warstwami walidacji i ochroną przed atakami. Upload plików jest jednym z najbardziej podatnych na ataki elementów aplikacji webowych, dlatego implementacja zawiera szereg zabezpieczeń.

**Walidacja typu MIME** jest wykonywana przez funkcję `mime_content_type()`, która sprawdza rzeczywistą zawartość pliku, a nie tylko rozszerzenie. System akceptuje tylko obrazy w formatach JPEG, PNG, GIF oraz WebP, co zapobiega uploadowi plików wykonywalnych, skryptów lub innych niebezpiecznych typów plików. Lista dozwolonych typów MIME jest zdefiniowana jako stała w klasie, co ułatwia zarządzanie i zapewnia spójność.

**Ograniczenie rozmiaru pliku** jest ustawione na 10MB, co zapobiega przeciążeniu serwera i nadmiernemu wykorzystaniu przestrzeni dyskowej. Limit jest sprawdzany przed zapisem pliku, co oszczędza zasoby serwera. Dodatkowo, PHP jest skonfigurowane z odpowiednimi limitami (`upload_max_filesize`, `post_max_size`) w pliku konfiguracyjnym Docker, co zapewnia spójność na poziomie całego systemu.

**Generowanie unikalnych nazw plików** wykorzystuje funkcję `uniqid()` z prefiksem `attachment_`, co zapewnia unikalność nazw i zapobiega nadpisaniu istniejących plików. Rozszerzenie pliku jest zachowywane z oryginalnej nazwy, co pozwala prawidłowo wyświetlać obrazy. Unikalne nazwy chronią też przed atakami polegającymi na przewidzeniu nazwy pliku i umożliwiają bezpieczne przechowywanie wielu plików w jednym katalogu.

**Przechowywanie poza katalogiem publicznym** jest bardzo ważne dla bezpieczeństwa. Pliki są zapisywane w katalogu `/mnt/szrotomoto_data`, który nie jest bezpośrednio dostępny przez HTTP. Dostęp do plików odbywa się tylko przez endpoint `attachments/show.php`, który sprawdza istnienie pliku i zwraca go z odpowiednim nagłówkiem Content-Type. Dzięki temu można dodatkowo kontrolować dostęp i zapobiegać bezpośredniemu linkowaniu do plików. W przypadku błędu podczas zapisu metadanych do bazy danych, plik jest automatycznie usuwany, co zapobiega powstawaniu "osieroconych" plików.

## 7. Narzędzia i Wzorce Projektowe

### 7.1 QueryBuilder

Zaimplementowano własny **Query Builder** w stylu fluent interface, który znacznie ułatwia czytanie i utrzymanie kodu związanego z zapytaniami do bazy danych. Fluent interface pozwala na łańcuchowe wywoływanie metod, co tworzy kod przypominający naturalny język, łatwy do zrozumienia i modyfikacji.

Query Builder pozwala budować złożone zapytania SQL w prosty sposób - opisujesz co chcesz dostać, a nie jak to ma działać. Na przykład, zamiast pisania długiego, skomplikowanego zapytania SQL z wieloma JOIN-ami, można użyć czytelnego łańcucha metod: `select()->from()->join()->where()->orderBy()`. Taki kod jest znacznie bardziej czytelny oraz łatwiejszy do zrozumienia dla innych deweloperów.

Ochrona przed SQL injection działa dzięki użyciu parametryzowanych zapytań. Query Builder automatycznie przygotowuje zapytania z użyciem prepared statements PDO, gdzie wszystkie wartości są przekazywane jako parametry. Metoda `setParameter()` pozwala na bezpieczne wiązanie wartości z parametrami zapytania, co całkowicie eliminuje ryzyko SQL injection, nawet jeśli użytkownik wprowadzi złośliwe dane.

Query Builder wspiera wszystkie najczęściej używane konstrukcje SQL, w tym różne typy JOIN-ów (INNER, LEFT, RIGHT), złożone warunki WHERE z operatorami AND/OR, sortowanie ORDER BY z możliwością wielu kolumn, oraz paginację poprzez LIMIT i OFFSET. Metoda `buildQuery()` buduje finalne zapytanie SQL z wszystkich ustawionych parametrów, zapewniając poprawną składnię i kolejność klauzul SQL.

Dzięki temu łatwiej utrzymywać i refaktoryzować zapytania - zmiany wymagają modyfikacji tylko w jednym miejscu, a nie w rozproszonych fragmentach SQL w różnych plikach. Query Builder ułatwia też testowanie, bo można łatwo podmienić implementację lub dodać logowanie zapytań do debugowania.

### 7.2 Singleton dla Bazy Danych

Klasa `Database` implementuje wzorzec **Singleton**, dzięki czemu w całej aplikacji jest tylko jedno połączenie z bazą danych. Wzorzec ten jest szczególnie ważny w kontekście aplikacji webowych, gdzie każde żądanie HTTP może potencjalnie utworzyć nowe połączenie, co prowadziłoby do nadmiernego wykorzystania zasobów i problemów z wydajnością.

**Jedno połączenie na żądanie** jest zapewnione przez przechowywanie instancji PDO w statycznej zmiennej `$pdo`. Przy pierwszym wywołaniu `getPdo()`, połączenie jest tworzone i zapisywane, a wszystkie kolejne wywołania zwracają tę samą instancję. Dzięki temu nie trzeba nawiązywać połączenia przy każdym zapytaniu, co jest wydajniejsze. Połączenie jest konfigurowane z kodowaniem UTF-8 (`charset=utf8` w DSN) i opcjami PDO zapewniającymi bezpieczeństwo: `ERRMODE_EXCEPTION` dla automatycznego rzucania wyjątków przy błędach, `FETCH_ASSOC` jako domyślny tryb pobierania danych, oraz `EMULATE_PREPARES => false` dla prawdziwych prepared statements.

**Leniwe inicjalizowanie** (lazy initialization) oznacza, że połączenie z bazą danych jest tworzone dopiero wtedy, gdy jest naprawdę potrzebne, a nie przy ładowaniu klasy. Metoda `getPdo()` sprawdza, czy połączenie już istnieje, i tworzy je tylko wtedy, gdy jest to konieczne. Dodatkowo, konfiguracja (parametry połączenia) jest ładowana też leniwie, dopiero przy pierwszym użyciu, co przyspiesza start aplikacji i pozwala łatwo testować bez konfiguracji bazy danych.

**Konfiguracja** jest przechowywana w zmiennych środowiskowych, które odczytuje klasa `Env`. Klasa `Env` wykorzystuje funkcję `parse_ini_file()` do odczytu pliku `.env` w formacie INI. Parametry połączenia (host, port, nazwa bazy, użytkownik, hasło) są w pliku `.env`, który nie jest commitowany do repozytorium, co zapewnia bezpieczeństwo wrażliwych danych. Klasa `Database` odczytuje te parametry przy pierwszym połączeniu przez `Env::get()` i używa ich do utworzenia DSN (Data Source Name) dla PDO. Dzięki temu można łatwo przełączać się między różnymi środowiskami (deweloperskie, testowe, produkcyjne) bez zmiany kodu.

### 7.3 Response Helper

Klasa `Response` formatuje odpowiedzi API w jednolity sposób, żeby nie powtarzać kodu związanego z wysyłaniem odpowiedzi HTTP. Klasa zawiera wszystkie standardowe kody statusu HTTP jako stałe, co zapewnia type safety i zapobiega błędom w kodach statusu.

**Jednolity format JSON** jest zapewniony przez metodę `json()`, która automatycznie ustawia nagłówek `Content-Type: application/json; charset=utf-8` i koduje dane do JSON używając `json_encode()` z flagami `JSON_UNESCAPED_UNICODE` (zachowuje polskie znaki) i `JSON_UNESCAPED_SLASHES` (nie escapuje ukośników). Metoda przyjmuje dane i opcjonalny kod statusu HTTP, co pozwala zwracać różne kody (200 dla sukcesu, 201 dla utworzenia zasobu, itd.) w spójny sposób. Metoda czyści bufor wyjściowy przed wysłaniem odpowiedzi i kończy wykonanie skryptu przez `exit`, więc żadne dodatkowe dane nie zostaną wysłane po odpowiedzi.

**Poprawne kody statusu HTTP** są ustawiane przez metodę `http_response_code()`, która jest wywoływana przed wysłaniem odpowiedzi. Klasa zawiera wszystkie standardowe kody HTTP jako stałe (od 100 do 511), co pozwala używać ich w czytelny sposób. Na przykład, zamiast magicznych liczb jak `404`, można użyć `Response::HTTP_NOT_FOUND`, co jest bardziej czytelne i mniej podatne na błędy.

**Zarządzanie nagłówkami** w jednym miejscu sprawia, że wszystkie odpowiedzi API mają takie same nagłówki, co jest ważne dla kompatybilności i bezpieczeństwa. Metoda `error()` to wygodny skrót do zwracania błędów - przyjmuje komunikat oraz kod statusu i automatycznie formatuje odpowiedź w formacie `{ "message": "..." }`. Dzięki temu obsługa błędów po stronie klienta jest łatwiejsza i zapewnia spójne doświadczenie użytkownika.

### 7.4 Stałe i Konfiguracja

Klasa `Consts` przechowuje wszystkie stałe wartości używane w aplikacji, takie jak statusy ofert, typy paliw, typy skrzyń biegów, typy nadwozia oraz listy krajów. Dzięki temu wszystkie wartości są w jednym miejscu, co ułatwia zarządzanie i zapewnia spójność.

Statusy ofert są zdefiniowane jako stałe (`OFFER_STATUS_ACTIVE`, `OFFER_STATUS_SOLD`, `OFFER_STATUS_REMOVED`), więc te same wartości są używane w całej aplikacji. Listy wartości enum (typy paliw, skrzynie biegów, itd.) są zwracane przez metody statyczne (`getFuelTypes()`, `getTransmissionTypes()`, itd.), co pozwala łatwo dodawać nowe wartości i zapewnia, że wszystkie części aplikacji używają aktualnej listy.

Dzięki temu zmiana listy dostępnych wartości wymaga modyfikacji tylko w jednym miejscu. Wszystkie endpointy używają tych samych list do walidacji, więc nie ma ryzyka, że w różnych miejscach będą różne wartości. Klasa `Consts` jest też łatwa do testowania i może być rozszerzona o dodatkowe funkcjonalności, takie jak walidacja wartości czy tłumaczenia.

## 8. Deployment i Środowisko

### 8.1 Docker

Aplikacja została skonteneryzowana przy użyciu Docker Compose, co pozwala łatwo zarządzać całym środowiskiem deweloperskim i zapewnia spójność między różnymi maszynami. Docker Compose zarządza wieloma kontenerami, definiując ich konfigurację, zależności i sieci w jednym pliku `docker-compose.yml`.

Kontener **backend** jest oparty na obrazie `php:8.4-apache`, który zawiera najnowszą wersję PHP wraz z serwerem Apache. Kontener został skonfigurowany z rozszerzeniem `pdo_mysql` do komunikacji z bazą danych oraz z włączonym modułem `mod_rewrite` Apache. Dodatkowo, kontener ma skonfigurowane limity uploadu plików (10MB) oraz lokalizację polską (pl_PL.UTF-8) i strefę czasową (Europe/Warsaw). Kontener jest dostępny na porcie 3000 (mapowanie `3000:80`), co pozwala komunikować się z frontendem działającym na porcie 80.

Kontener **db** wykorzystuje oficjalny obraz `mysql:8.4` z prekonfigurowaną bazą danych `szrotomoto` oraz użytkownikiem aplikacji. Baza danych jest dostępna na porcie 3307 (mapowanie `3307:3306`) oraz przechowywana w wolumenie Docker `db_data`, co zapewnia trwałość danych między restartami kontenerów. Wszystkie kontenery są skonfigurowane z platformą `linux/amd64` dla zapewnienia kompatybilności.

Kontener **frontend** jest osobnym serwisem, co pozwala na niezależne skalowanie i zarządzanie frontendem i backendem. Frontend jest dostępny na porcie 80 (mapowanie `80:80`). 

Kontenery komunikują się przez dedykowane sieci Docker: sieć `private` łączy backend z bazą danych, zapewniając bezpieczną komunikację bez ekspozycji na zewnętrzne interfejsy, podczas gdy sieć `public` umożliwia komunikację między frontendem a backendem. Taka architektura sieci zapewnia izolację i bezpieczeństwo, bo baza danych nie jest bezpośrednio dostępna z zewnątrz kontenerów. Dodatkowo, kontener backend ma zamontowany wolumen `szrotomoto_data` w katalogu `/mnt/szrotomoto_data` do przechowywania załączników (zdjęć pojazdów), co zapewnia trwałość plików między restartami kontenera.

Docker Compose automatycznie zarządza zależnościami między kontenerami (`depends_on`), zapewniając, że baza danych jest gotowa przed uruchomieniem backendu, a backend jest gotowy przed uruchomieniem frontendu. Healthcheck dla bazy danych dodatkowo weryfikuje, czy MySQL jest gotowy do przyjmowania połączeń, co zwiększa niezawodność uruchamiania aplikacji.

### 8.2 Konfiguracja

Konfiguracja aplikacji jest w pliku `.env`, który to standardowy sposób zarządzania konfiguracją w aplikacjach webowych. Plik `.env` nie jest commitowany do repozytorium (jest w `.gitignore`), co zapewnia bezpieczeństwo wrażliwych danych oraz umożliwia różne konfiguracje dla różnych środowisk.

Plik `.env` zawiera **parametry połączenia z bazą danych**, w tym host (`DB_HOST`), port (`DB_PORT`), nazwę bazy danych (`DB_NAME`), użytkownika (`DB_USER`) oraz hasło (`DB_PASSWORD`). Te parametry są odczytywane przez klasę `Env` przy użyciu funkcji `parse_ini_file()`, która parsuje plik w formacie INI. W kontekście Docker, plik `.env` jest montowany jako read-only volume w kontenerach, co zapewnia, że zmiany w konfiguracji nie wymagają przebudowy obrazów.

**Ustawienia lokalizacji i strefy czasowej** są ustawiane zarówno w pliku `.env`, jak i bezpośrednio w kontenerach Docker. Kontenery mają ustawione zmienne środowiskowe `LANG`, `LANGUAGE`, `LC_ALL` oraz `TZ`, co zapewnia spójne formatowanie dat, liczb oraz komunikatów błędów w całej aplikacji. Polska lokalizacja jest szczególnie ważna dla aplikacji skierowanej do polskich użytkowników, ponieważ zapewnia poprawne formatowanie dat oraz komunikatów.

**Inne zmienne środowiskowe** można łatwo dodać do pliku `.env` i użyć w aplikacji przez klasę `Env`. Dzięki temu można elastycznie konfigurować aplikację bez zmiany kodu, co jest przydatne przy wdrażaniu na różnych środowiskach (deweloperskie, testowe, produkcyjne).

### 8.3 Migracje i Seeding

System migracji pozwala łatwo wdrażać zmiany w schemacie bazy danych i zapewnia kontrolę wersji struktury bazy danych. Skrypt `backend/bin/migrate.php` odczytuje wszystkie pliki SQL z katalogu `migrations/`, sortuje je chronologicznie na podstawie timestampu w nazwie pliku, a następnie wykonuje tylko te migracje, które jeszcze nie zostały zastosowane.

Mechanizm śledzenia wykonanych migracji działa przez tabelę `migrations` w bazie danych, która przechowuje nazwy wykonanych plików migracyjnych (`migration`), timestamp wykonania (`applied_at`) oraz czas wykonania w sekundach (`runtime`). Przed wykonaniem każdej migracji, skrypt sprawdza, czy dana migracja już została zastosowana, porównując listę dostępnych plików z listą wykonanych migracji w bazie danych. Dzięki temu migracja nie zostanie wykonana dwa razy i można śledzić historię zmian oraz wydajność migracji. To jest szczególnie ważne w środowisku zespołowym, gdzie różni deweloperzy mogą mieć różne wersje schematu bazy danych.

Dodatkowo dostępny jest seeder (`backend/bin/seed.php`) do wstępnego załadowania danych testowych, takich jak przykładowe marki i modele pojazdów. Seeder odczytuje dane z pliku JSON (`vechicles.json`) zawierającego strukturę marek i modeli, co ułatwia zarządzanie danymi referencyjnymi. Seeder wykorzystuje transakcje bazy danych, więc jeśli wystąpi błąd, wszystkie zmiany są cofane. Przed załadowaniem danych, seeder usuwa istniejące rekordy z tabel `models` i `brands`, co pozwala na czyste załadowanie danych. Seeder wykorzystuje klasę `ArrayUtils` do mapowania danych i tych samych mechanizmów dostępu do bazy danych co reszta aplikacji, co zapewnia spójność. W przeciwieństwie do migracji, seeder może być uruchamiany wielokrotnie, co pozwala resetować dane testowe do stanu początkowego.

## 9. Podsumowanie

Backend projektu Szrotomoto został zaprojektowany z naciskiem na jakość kodu, bezpieczeństwo i utrzymywalność. Projekt pokazuje zastosowanie dobrych praktyk programistycznych i wzorców projektowych w aplikacji webowej.

**Czytelność kodu** została osiągnięta dzięki jasnej strukturze projektu z wyraźnym podziałem na warstwy i logiczną organizacją plików. Separacja odpowiedzialności między warstwami (API, logika biznesowa, dostęp do danych, prezentacja) ułatwia zrozumienie systemu i pozwala na równoległą pracę nad różnymi częściami aplikacji. Użycie czytelnych nazw klas, metod i zmiennych dodatkowo poprawia czytelność kodu.

**Bezpieczeństwo** jest zapewnione na wielu poziomach: walidacja danych wejściowych zapobiega wprowadzaniu błędnych lub złośliwych danych, autoryzacja kontroluje dostęp do zasobów, parametryzowane zapytania chronią przed SQL injection, a bezpieczne przechowywanie haseł (hashe) i sesji zapewnia ochronę danych użytkowników. System uploadu plików ma wielowarstwową walidację, co zapobiega uploadowi niebezpiecznych plików.

**Skalowalność** została uwzględniona w architekturze systemu dzięki modularnej strukturze i elastycznemu API. Dodawanie nowych funkcjonalności nie wymaga zmiany istniejącego kodu, wystarczy dodać nowe endpointy lub rozszerzyć istniejące klasy. Query Builder oraz centralne klasy pomocnicze ułatwiają rozwijanie funkcjonalności bez duplikacji kodu.

**Utrzymywalność** jest wspierana przez użycie wzorców projektowych (Singleton, Fluent Interface) i dobrych praktyk programistycznych. System migracji pozwala na kontrolowane zmiany w schemacie bazy danych, a przechowywanie stałych i konfiguracji w jednym miejscu ułatwia zarządzanie aplikacją. Kod jest zorganizowany w sposób, który ułatwia testowanie i refaktoryzację.

**Dokumentacja** została przygotowana w formacie Markdown, co ułatwia czytanie i pozwala na automatyczne generowanie dokumentacji. Dokumentacja API (`ENDPOINTS.md`) zawiera szczegółowe opisy wszystkich endpointów wraz z przykładami żądań i odpowiedzi, co ułatwia integrację i rozwój aplikacji.

System spełnia wymagania funkcjonalne aplikacji do zarządzania ofertami pojazdów i zapewnia solidne fundamenty do dalszego rozwoju. Architektura i implementacja są gotowe na rozszerzenie o dodatkowe funkcjonalności, takie jak wyszukiwanie zaawansowane, powiadomienia, system ocen użytkowników czy integracja z zewnętrznymi serwisami.

