# Sprawozdanie z Projektu - Szrotomoto

## 1. Wprowadzenie

Projekt **Szrotomoto** jest aplikacją webową służącą do zarządzania ofertami sprzedaży pojazdów. System umożliwia użytkownikom przeglądanie, tworzenie, edycję oraz zarządzanie ofertami samochodów. Aplikacja została zaprojektowana z myślą o użytkownikach pragnących kupować lub sprzedawać pojazdy, oferując intuicyjny interfejs oraz kompleksowe zarządzanie danymi technicznymi pojazdów.

Backend został zaprojektowany jako RESTful API oparte na PHP, które komunikuje się z frontendem poprzez standardowe żądania HTTP. Decyzja o zastosowaniu architektury API pozwala na elastyczność w implementacji frontendu oraz umożliwia potencjalne wykorzystanie tego samego backendu przez różne aplikacje klienckie (web, mobilne, desktopowe). API zostało zbudowane z naciskiem na czytelność, bezpieczeństwo oraz łatwość utrzymania, co jest kluczowe dla długoterminowego rozwoju projektu.

## 2. Architektura i Stack Technologiczny

### 2.1 Technologie

Backend został zaimplementowany w **PHP 8.4**, co zapewnia dostęp do najnowszych funkcjonalności języka oraz optymalizacji wydajnościowych. Wybór PHP jako technologii głównej podyktowany był jego powszechnym zastosowaniem w aplikacjach webowych, bogatym ekosystemem oraz łatwością wdrożenia.

Jako serwer webowy wykorzystano **Apache HTTP Server**, który zapewnia stabilność i szerokie wsparcie dla aplikacji PHP. Apache został skonfigurowany z modułem `mod_rewrite`, umożliwiającym przyjazne adresy URL oraz przekierowania. **MySQL 8.4** został wybrany jako system zarządzania bazą danych ze względu na jego niezawodność, wydajność oraz szerokie wsparcie w środowisku PHP. Komunikacja z bazą danych odbywa się poprzez **PDO** (PHP Data Objects), które zapewnia bezpieczne, parametryzowane zapytania chroniące przed atakami SQL injection.

Całość została skonteneryzowana przy użyciu **Docker**, co umożliwia łatwe zarządzanie środowiskiem deweloperskim oraz zapewnia spójność między różnymi maszynami deweloperskimi. Docker Compose koordynuje działanie wielu kontenerów (backend, frontend, baza danych), zapewniając izolację oraz kontrolę nad sieciami i wolumenami.

### 2.2 Architektura Systemu

Aplikacja została zaprojektowana w architekturze **warstwowej** z wyraźnym podziałem odpowiedzialności. Taki podział zapewnia modularność systemu oraz ułatwia jego rozwój i utrzymanie. Każda warstwa ma jasno określone zadania i odpowiedzialności, co minimalizuje zależności między komponentami.

**Warstwa API** (`backend/api/`) zawiera endpointy obsługujące żądania HTTP. Każdy endpoint jest osobnym plikiem PHP, który odpowiada za obsługę konkretnego zasobu lub operacji. Endpointy są zorganizowane w logiczne katalogi odpowiadające domenom biznesowym (np. `login/`, `offers/`, `vehicles/`), co ułatwia nawigację po kodzie. **Warstwa logiki biznesowej** (`backend/utils/`) zawiera klasy pomocnicze i narzędzia wykorzystywane przez endpointy. Klasy te enkapsulują wspólną logikę, eliminując duplikację kodu i zapewniając spójność implementacji.

**Warstwa dostępu do danych** jest abstrahowana przez klasę `Database` oraz `QueryBuilder`. Klasa `Database` zarządza połączeniami z bazą danych, zapewniając singleton pattern dla efektywnego wykorzystania zasobów. `QueryBuilder` oferuje fluent interface do budowania zapytań SQL, co zwiększa czytelność kodu i zmniejsza ryzyko błędów. **Warstwa prezentacji** jest reprezentowana przez klasę `Response`, która standaryzuje format odpowiedzi API. Wszystkie odpowiedzi są zwracane w formacie JSON z odpowiednimi kodami statusu HTTP, co zapewnia spójność interfejsu API.

Komunikacja między warstwami odbywa się poprzez jasno zdefiniowane interfejsy, co zapewnia separację odpowiedzialności i ułatwia testowanie oraz utrzymanie kodu. Taka architektura umożliwia również łatwą wymianę implementacji poszczególnych warstw bez wpływu na pozostałe komponenty systemu.

## 3. Projekt Bazy Danych

### 3.1 Schemat Relacyjny

Baza danych została zaprojektowana zgodnie z zasadami normalizacji, co zapewnia efektywne przechowywanie danych oraz minimalizuje redundancję. Projekt schematu uwzględnia relacje między encjami oraz zapewnia integralność referencyjną poprzez wykorzystanie kluczy obcych.

Tabela **users** przechowuje dane użytkowników systemu, w tym unikalny adres email, zahashowane hasło oraz imię użytkownika. Email jest unikalny w całej bazie, co zapobiega duplikatom kont. Hasła są przechowywane jako hashe generowane przez funkcję `password_hash()` PHP, co zapewnia bezpieczeństwo wrażliwych danych. Tabela **brands** zawiera katalog marek pojazdów dostępnych w systemie. Każda marka ma unikalną nazwę oraz timestamp utworzenia, co umożliwia śledzenie historii dodawania marek.

Tabela **models** przechowuje modele pojazdów powiązane z markami poprzez relację wiele-do-jednego. Każdy model musi być przypisany do istniejącej marki, co zapewnia spójność danych. Kombinacja `brand_id` i `name` jest unikalna, co zapobiega duplikatom modeli w ramach jednej marki. Tabela **offers** jest centralną tabelą systemu, przechowującą oferty pojazdów wraz z pełnymi danymi technicznymi. Zawiera ona ponad 20 pól opisujących pojazd, w tym dane podstawowe (cena, rok produkcji, przebieg), parametry techniczne (moc, moment obrotowy, pojemność), oraz informacje o stanie pojazdu (historia wypadków, gwarancja, książka serwisowa).

Tabela **attachments** przechowuje metadane załączników (zdjęcia pojazdów), w tym unikalną nazwę pliku oraz typ MIME. Pliki są fizycznie przechowywane w systemie plików, a baza danych zawiera jedynie referencje do nich. Tabela **migrations** służy do śledzenia wykonanych migracji bazy danych, co umożliwia kontrolę wersji schematu oraz zapewnia, że każda migracja zostanie wykonana tylko raz.

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
- offers.model_id → models.id (FK z ON DELETE CASCADE)
- offers.created_by → users.id (FK)
- models.brand_id → brands.id (FK z ON DELETE CASCADE)
- offers.attachments przechowuje tablicę ID załączników w JSON
  (relacja logiczna, nie fizyczny klucz obcy)
```

### 3.2 Kluczowe Decyzje Projektowe

**Integralność referencyjna** została zapewniona poprzez wykorzystanie kluczy obcych z opcją `ON DELETE CASCADE`. Oznacza to, że usunięcie marki automatycznie usuwa wszystkie powiązane modele, a usunięcie modelu powoduje usunięcie wszystkich ofert wykorzystujących ten model. Taka strategia zapewnia spójność danych oraz zapobiega powstawaniu "sierocych" rekordów w bazie danych. Decyzja ta została podjęta z uwagi na fakt, że w kontekście aplikacji nie ma sensu przechowywać modeli bez marki lub ofert bez modelu.

**Unikalność VIN** została zaimplementowana na poziomie bazy danych oraz aplikacji. Numer VIN (Vehicle Identification Number) jest unikalnym identyfikatorem pojazdu, więc jego duplikacja w systemie byłaby błędem logicznym. Przed utworzeniem oferty system sprawdza, czy VIN już istnieje w bazie, zwracając odpowiedni kod błędu (409 Conflict) w przypadku próby duplikacji. Ta walidacja zapobiega przypadkowemu lub celowemu tworzeniu duplikatów ofert dla tego samego pojazdu.

**JSON dla załączników** - tabela `offers` przechowuje tablicę ID załączników w formacie JSON w kolumnie `attachments`. Ta decyzja projektowa umożliwia elastyczne zarządzanie wieloma zdjęciami dla jednej oferty bez konieczności tworzenia dodatkowej tabeli relacyjnej. JSON pozwala na łatwe dodawanie i usuwanie załączników, a także na zachowanie kolejności zdjęć. Alternatywne rozwiązanie z osobną tabelą relacyjną byłoby bardziej normalizowane, ale wymagałoby dodatkowych zapytań JOIN przy pobieraniu ofert.

**Statusy ofert** zostały zaimplementowane jako system zarządzania cyklem życia oferty. Oferta może mieć status `active` (aktywna, widoczna dla wszystkich), `sold` (sprzedana, widoczna ale oznaczona jako niedostępna) lub `removed` (usunięta, widoczna tylko dla twórcy). System statusów umożliwia użytkownikom zarządzanie swoimi ofertami bez konieczności ich trwałego usuwania, co może być przydatne do celów historycznych lub statystycznych. Logika biznesowa zapewnia, że tylko aktywne oferty są widoczne w wynikach wyszukiwania, podczas gdy nieaktywne oferty są dostępne tylko dla ich twórców.

### 3.3 Migracje

System migracji bazy danych (`backend/bin/migrate.php`) umożliwia wersjonowanie zmian w schemacie oraz kontrolowane wdrażanie aktualizacji struktury bazy danych. Każda migracja jest plikiem SQL z timestampem w nazwie (format: `YYYYMMDDHHMM.sql`), co pozwala na automatyczne sortowanie migracji chronologicznie oraz śledzenie historii zmian.

Skrypt migracyjny odczytuje wszystkie pliki SQL z katalogu `migrations/`, porównuje je z listą wykonanych migracji w tabeli `migrations`, a następnie wykonuje tylko te migracje, które jeszcze nie zostały zastosowane. Taki mechanizm zapewnia, że każda migracja zostanie wykonana dokładnie raz, nawet jeśli skrypt zostanie uruchomiony wielokrotnie. System migracji jest kluczowy dla współpracy zespołowej oraz wdrażania aplikacji na różnych środowiskach (deweloperskie, testowe, produkcyjne), zapewniając spójność schematu bazy danych we wszystkich środowiskach.

## 4. Projekt API

### 4.1 RESTful Design

API zostało zaprojektowane zgodnie z zasadami REST (Representational State Transfer), co zapewnia spójność, przewidywalność oraz łatwość w użyciu. REST jest architekturą opartą na zasobach, gdzie każdy zasób jest identyfikowany przez unikalny URL, a operacje na zasobach są wykonywane poprzez standardowe metody HTTP.

**Zasoby** są reprezentowane przez ścieżki URL, które są zorganizowane hierarchicznie i semantycznie. Na przykład `/api/offers/` reprezentuje kolekcję ofert, podczas gdy `/api/offers/show.php?offer_id=123` reprezentuje konkretną ofertę. Taka organizacja URL-i jest intuicyjna i łatwa do zrozumienia zarówno dla deweloperów, jak i dla narzędzi automatyzujących (np. Postman, curl).

**Metody HTTP** określają operacje wykonywane na zasobach. Metoda GET jest używana do odczytu danych (np. pobieranie listy ofert, szczegółów oferty), podczas gdy POST jest wykorzystywana do tworzenia nowych zasobów (rejestracja, tworzenie oferty) oraz modyfikacji istniejących (edycja oferty). Taki podział metod zapewnia semantyczną jasność operacji oraz umożliwia optymalizację na poziomie serwera (np. cache dla żądań GET).

**Kody statusu HTTP** informują o wyniku operacji, co jest kluczowe dla prawidłowej obsługi odpowiedzi po stronie klienta. Kod 200 oznacza sukces, 201 oznacza utworzenie nowego zasobu, 400 sygnalizuje błąd walidacji, 401 oznacza brak autentykacji, 403 wskazuje na brak uprawnień, 404 oznacza brak zasobu, 409 sygnalizuje konflikt (np. duplikat), a 500 oznacza błąd serwera. **Format JSON** jest używany dla wszystkich odpowiedzi, co zapewnia spójność oraz łatwość parsowania po stronie klienta. JSON jest również czytelny dla ludzi, co ułatwia debugowanie i rozwój aplikacji.

### 4.2 Struktura Endpointów

API zostało zorganizowane w logiczne grupy odpowiadające domenom biznesowym aplikacji. Taka organizacja ułatwia nawigację po kodzie, utrzymanie oraz rozwijanie funkcjonalności. Każda grupa endpointów jest umieszczona w osobnym katalogu, co zapewnia czytelną strukturę projektu.

**Autentykacja** (`/api/login/`) zawiera endpointy odpowiedzialne za zarządzanie sesją użytkownika. `login.php` obsługuje proces logowania, weryfikując dane użytkownika i tworząc sesję. `register.php` umożliwia rejestrację nowych użytkowników z walidacją danych wejściowych oraz sprawdzaniem unikalności emaila. `logout.php` kończy sesję użytkownika, niszcząc dane sesyjne. `me.php` pozwala na sprawdzenie aktualnego statusu autentykacji, co jest wykorzystywane przez frontend do dynamicznego dostosowywania interfejsu użytkownika.

**Oferty** (`/api/offers/`) to największa grupa endpointów, odpowiadająca za zarządzanie ofertami pojazdów. `search.php` zwraca listę wszystkich aktywnych ofert z pełnymi danymi, włączając informacje o marce, modelu oraz użytkowniku. `show.php` pobiera szczegóły pojedynczej oferty, z logiką zapewniającą, że nieaktywne oferty są widoczne tylko dla ich twórców. `create.php` obsługuje tworzenie nowych ofert z kompleksową walidacją danych oraz możliwością jednoczesnego uploadu zdjęć. `edit.php` umożliwia modyfikację istniejących ofert, z weryfikacją uprawnień oraz obsługą częściowych aktualizacji. `setAsSold.php` i `setAsRemoved.php` pozwalają na zmianę statusu oferty, co jest prostsze niż pełna edycja.

**Pojazdy** (`/api/vehicles/`) dostarcza danych referencyjnych o pojazdach. `brands.php` zwraca listę wszystkich dostępnych marek, posortowaną alfabetycznie. `models.php` zwraca listę modeli, z opcjonalnym filtrem po marce (`brand_id`), co umożliwia dynamiczne ładowanie modeli w interfejsie użytkownika w zależności od wybranej marki.

**Wartości statyczne** (`/api/values/`) zawiera endpointy zwracające listy wartości używanych w formularzach, takie jak typy paliw, typy skrzyń biegów, typy nadwozia oraz lista krajów. Te endpointy są wykorzystywane do dynamicznego generowania pól formularzy oraz walidacji danych wejściowych.

**Załączniki** (`/api/attachments/`) zarządza plikami graficznymi. `create.php` obsługuje upload zdjęć z walidacją typu i rozmiaru pliku, zwracając ID załącznika, który może być następnie powiązany z ofertą. `show.php` służy do wyświetlania zdjęć, zwracając plik z odpowiednim nagłówkiem Content-Type, co umożliwia bezpośrednie wyświetlanie obrazów w przeglądarce.

### 4.3 Walidacja i Obsługa Błędów

Każdy endpoint implementuje wielowarstwową walidację danych wejściowych, co jest kluczowe dla bezpieczeństwa oraz niezawodności aplikacji. Walidacja odbywa się na kilku poziomach, począwszy od podstawowych sprawdzeń, aż po złożone reguły biznesowe.

**Sprawdzanie wymaganych pól** jest pierwszym krokiem walidacji. Endpointy weryfikują, czy wszystkie pola oznaczone jako wymagane zostały przesłane w żądaniu. W przypadku braku wymaganych pól, system zwraca błąd 400 Bad Request z komunikatem wskazującym brakujące pola. Ta walidacja zapobiega przetwarzaniu niekompletnych danych oraz zapewnia, że wszystkie krytyczne informacje są dostępne.

**Walidacja formatów** obejmuje sprawdzanie poprawności formatów danych, takich jak email (poprawność składni adresu email) oraz VIN (Vehicle Identification Number). Email jest walidowany przy użyciu wbudowanych funkcji PHP, co zapewnia zgodność ze standardami RFC. VIN jest sprawdzany pod kątem unikalności oraz poprawności formatu, co zapobiega wprowadzaniu błędnych danych.

**Weryfikacja wartości enum** zapewnia, że wartości takie jak typ paliwa, typ skrzyni biegów czy typ nadwozia należą do dozwolonej listy wartości. Listy te są centralnie zarządzane w klasie `Consts`, co zapewnia spójność oraz ułatwia aktualizację dostępnych opcji. Próba użycia nieprawidłowej wartości skutkuje błędem 400 Bad Request.

**Sprawdzanie unikalności** jest wykonywane dla pól, które muszą być unikalne w systemie, takich jak email użytkownika oraz VIN pojazdu. Przed utworzeniem nowego rekordu system wykonuje zapytanie do bazy danych, sprawdzając, czy wartość już istnieje. W przypadku duplikatu zwracany jest błąd 409 Conflict, co jest semantycznie poprawne dla sytuacji konfliktu zasobów.

**Autoryzacja** jest weryfikowana na poziomie endpointów wymagających uprawnień. System sprawdza nie tylko, czy użytkownik jest zalogowany, ale także, czy ma uprawnienia do wykonania konkretnej operacji. Na przykład, tylko właściciel oferty może ją edytować lub zmienić jej status. Próba wykonania nieautoryzowanej operacji skutkuje błędem 403 Forbidden.

Błędy są zwracane w spójnym formacie JSON z odpowiednimi kodami HTTP, co ułatwia obsługę po stronie klienta. Każdy błąd zawiera pole `message` z opisem problemu, co pozwala aplikacji klienckiej na wyświetlenie użytkownikowi zrozumiałego komunikatu. Spójny format odpowiedzi błędów ułatwia również debugowanie oraz integrację z różnymi klientami API.

## 5. Bezpieczeństwo i Autentykacja

### 5.1 System Sesji

Autentykacja opiera się na **sesjach PHP** z wykorzystaniem cookies, co jest standardowym i bezpiecznym rozwiązaniem dla aplikacji webowych. Sesje PHP są zarządzane przez serwer, a identyfikator sesji jest przechowywany w cookie po stronie klienta. Taki mechanizm zapewnia, że dane sesyjne są przechowywane bezpiecznie po stronie serwera, a klient otrzymuje jedynie identyfikator sesji.

Klasa `Session` została zaprojektowana jako warstwa abstrakcji nad natywnymi funkcjami sesji PHP, co zapewnia centralne zarządzanie sesjami oraz spójne API w całej aplikacji. Klasa implementuje wzorzec Singleton dla sesji, zapewniając, że sesja jest inicjalizowana tylko raz na żądanie. Metoda `start()` sprawdza status sesji i inicjalizuje ją tylko wtedy, gdy nie została jeszcze uruchomiona, co zapobiega konfliktom oraz zapewnia wydajność.

Metody kontroli dostępu `allowAuthenticatedOnly()` oraz `allowUnauthenticatedOnly()` zapewniają deklaratywną kontrolę dostępu na poziomie endpointów. `allowAuthenticatedOnly()` sprawdza, czy użytkownik jest zalogowany, i zwraca błąd 401 Unauthorized w przeciwnym przypadku. `allowUnauthenticatedOnly()` zapobiega dostępowi do endpointów (np. rejestracja, logowanie) dla już zalogowanych użytkowników, zwracając błąd 400 Bad Request. Te metody eliminują konieczność powtarzania kodu sprawdzającego autentykację w każdym endpoincie.

Dane użytkownika są przechowywane w sesji w bezpieczny sposób. Po udanym logowaniu, system zapisuje w sesji flagę `is_authenticated`, identyfikator użytkownika (`user_id`) oraz imię użytkownika (`user_name`). Te dane są wykorzystywane przez endpointy do identyfikacji użytkownika oraz do wyświetlania informacji w interfejsie. Metoda `logout()` czyści wszystkie dane sesyjne i niszczy sesję, zapewniając bezpieczne wylogowanie użytkownika.

### 5.2 Hasła

Hasła użytkowników są przechowywane jako hashe z wykorzystaniem funkcji `password_hash()` PHP, która implementuje bezpieczny algorytm hashowania (domyślnie bcrypt). Funkcja ta automatycznie generuje sól (salt) dla każdego hasła, co zapewnia, że nawet identyczne hasła będą miały różne hashe. Dodatkowo, funkcja `password_hash()` obsługuje automatyczne dostosowanie kosztu obliczeniowego, co pozwala na zwiększenie bezpieczeństwa wraz z rozwojem mocy obliczeniowej.

Podczas rejestracji, hasło użytkownika jest hashowane przed zapisaniem do bazy danych. Podczas logowania, system wykorzystuje funkcję `password_verify()` do porównania wprowadzonego hasła z hashem przechowywanym w bazie danych. Funkcja ta jest odporna na ataki timingowe, co jest ważne dla bezpieczeństwa. Hashe są przechowywane w kolumnie `password_hash` w tabeli `users`, a oryginalne hasła nigdy nie są przechowywane ani logowane, co jest zgodne z najlepszymi praktykami bezpieczeństwa.

System wymaga również, aby użytkownik potwierdził hasło podczas rejestracji (pole `repeated_password`), co zmniejsza ryzyko błędów w wprowadzaniu hasła. Walidacja sprawdza, czy oba hasła są identyczne przed zapisaniem użytkownika do bazy danych.

### 5.3 Autoryzacja

System implementuje kontrolę dostępu na poziomie endpointów, co zapewnia, że użytkownicy mogą wykonywać tylko te operacje, do których mają uprawnienia. Autoryzacja jest weryfikowana po autentykacji, co oznacza, że system najpierw sprawdza, czy użytkownik jest zalogowany, a następnie czy ma odpowiednie uprawnienia do wykonania operacji.

**Własność oferty** jest weryfikowana przed każdą operacją modyfikującą ofertę (edycja, zmiana statusu, usunięcie). System pobiera ofertę z bazy danych i porównuje pole `created_by` z identyfikatorem zalogowanego użytkownika. Jeśli użytkownik nie jest właścicielem oferty, operacja jest odrzucana z błędem 403 Forbidden. Ta kontrola jest kluczowa dla bezpieczeństwa, zapobiegając nieautoryzowanym modyfikacjom ofert innych użytkowników.

**Widoczność nieaktywnych ofert** jest kontrolowana przez logikę biznesową w endpoincie `show.php`. Gdy oferta ma status inny niż `active`, system sprawdza, czy żądanie pochodzi od właściciela oferty. Jeśli nie, zwracany jest błąd 404 Not Found, co ukrywa istnienie nieaktywnych ofert przed nieuprawnionymi użytkownikami. Ta logika zapewnia prywatność użytkowników oraz umożliwia im zarządzanie historią swoich ofert.

**Upload plików** wymaga autentykacji, co zapobiega nieautoryzowanemu wykorzystaniu zasobów serwera oraz chroni przed spamem. Endpoint `attachments/create.php` wykorzystuje metodę `Session::allowAuthenticatedOnly()` do wymuszenia autentykacji przed przetworzeniem uploadu. Dodatkowo, każdy załącznik jest powiązany z użytkownikiem, co umożliwia potencjalne śledzenie pochodzenia plików oraz zarządzanie przestrzenią dyskową per użytkownik.

### 5.4 Upload Plików

Klasa `AttachmentUploader` implementuje bezpieczny mechanizm uploadu plików z wielowarstwową walidacją oraz ochroną przed różnymi typami ataków. Upload plików jest jednym z najbardziej podatnych na ataki elementów aplikacji webowych, dlatego implementacja zawiera szereg zabezpieczeń.

**Walidacja typu MIME** jest wykonywana przy użyciu funkcji `mime_content_type()`, która analizuje rzeczywistą zawartość pliku, a nie tylko rozszerzenie. System akceptuje tylko obrazy w formatach JPEG, PNG, GIF oraz WebP, co zapobiega uploadowi plików wykonywalnych, skryptów lub innych niebezpiecznych typów plików. Lista dozwolonych typów MIME jest zdefiniowana jako stała w klasie, co ułatwia zarządzanie oraz zapewnia spójność.

**Ograniczenie rozmiaru pliku** jest ustawione na 10MB, co zapobiega przeciążeniu serwera oraz nadmiernemu wykorzystaniu przestrzeni dyskowej. Limit jest weryfikowany przed zapisem pliku, co oszczędza zasoby serwera. Dodatkowo, PHP jest skonfigurowane z odpowiednimi limitami (`upload_max_filesize`, `post_max_size`) w pliku konfiguracyjnym Docker, co zapewnia spójność na poziomie całego systemu.

**Generowanie unikalnych nazw plików** wykorzystuje funkcję `uniqid()` z prefiksem `attachment_`, co zapewnia unikalność nazw oraz zapobiega nadpisaniu istniejących plików. Rozszerzenie pliku jest zachowywane z oryginalnej nazwy, co umożliwia prawidłowe wyświetlanie obrazów. Unikalne nazwy chronią również przed atakami polegającymi na przewidzeniu nazwy pliku oraz umożliwiają bezpieczne przechowywanie wielu plików w jednym katalogu.

**Przechowywanie poza katalogiem publicznym** jest kluczowe dla bezpieczeństwa. Pliki są zapisywane w katalogu `/mnt/szrotomoto_data`, który nie jest bezpośrednio dostępny przez HTTP. Dostęp do plików odbywa się wyłącznie przez endpoint `attachments/show.php`, który weryfikuje istnienie pliku oraz zwraca go z odpowiednim nagłówkiem Content-Type. Taki mechanizm umożliwia dodatkową kontrolę dostępu oraz zapobiega bezpośredniemu linkowaniu do plików. W przypadku błędu podczas zapisu metadanych do bazy danych, plik jest automatycznie usuwany, co zapobiega powstawaniu "osieroconych" plików.

## 6. Narzędzia i Wzorce Projektowe

### 6.1 QueryBuilder

Zaimplementowano własny **Query Builder** w stylu fluent interface, który znacząco poprawia czytelność oraz utrzymywalność kodu związanego z zapytaniami do bazy danych. Fluent interface pozwala na łańcuchowe wywoływanie metod, co tworzy kod przypominający naturalny język, łatwy do zrozumienia i modyfikacji.

Query Builder umożliwia budowanie złożonych zapytań SQL w sposób deklaratywny, gdzie programista opisuje, co chce uzyskać, a nie jak to ma być zaimplementowane. Na przykład, zamiast pisania długiego, skomplikowanego zapytania SQL z wieloma JOIN-ami, można użyć czytelnego łańcucha metod: `select()->from()->join()->where()->orderBy()`. Taki kod jest znacznie bardziej czytelny oraz łatwiejszy do zrozumienia dla innych deweloperów.

Ochrona przed SQL injection jest zapewniona poprzez obowiązkowe użycie parametryzowanych zapytań. Query Builder automatycznie przygotowuje zapytania z użyciem prepared statements PDO, gdzie wszystkie wartości są przekazywane jako parametry. Metoda `setParameter()` pozwala na bezpieczne wiązanie wartości z parametrami zapytania, co całkowicie eliminuje ryzyko SQL injection, nawet jeśli użytkownik wprowadzi złośliwe dane.

Query Builder wspiera wszystkie najczęściej używane konstrukcje SQL, w tym różne typy JOIN-ów (INNER, LEFT, RIGHT), złożone warunki WHERE z operatorami AND/OR, sortowanie ORDER BY z możliwością wielu kolumn, oraz paginację poprzez LIMIT i OFFSET. Metoda `buildQuery()` konstruuje finalne zapytanie SQL z wszystkich ustawionych parametrów, zapewniając poprawną składnię oraz kolejność klauzul SQL.

Ułatwia to utrzymanie i refaktoryzację zapytań, ponieważ zmiany w strukturze zapytania wymagają modyfikacji tylko jednego miejsca w kodzie, a nie rozproszonych fragmentów SQL w różnych plikach. Dodatkowo, Query Builder ułatwia testowanie, ponieważ można łatwo podmienić implementację lub dodać logowanie zapytań dla celów debugowania.

### 6.2 Singleton dla Bazy Danych

Klasa `Database` implementuje wzorzec **Singleton**, który zapewnia, że w całej aplikacji istnieje tylko jedno połączenie z bazą danych. Wzorzec ten jest szczególnie ważny w kontekście aplikacji webowych, gdzie każde żądanie HTTP może potencjalnie utworzyć nowe połączenie, co prowadziłoby do nadmiernego wykorzystania zasobów oraz problemów z wydajnością.

**Jedno połączenie na żądanie** jest zapewnione poprzez przechowywanie instancji PDO w statycznej zmiennej `$pdo`. Przy pierwszym wywołaniu `getPdo()`, połączenie jest tworzone i zapisywane, a wszystkie kolejne wywołania zwracają tę samą instancję. Taki mechanizm jest wydajny, ponieważ unika kosztownego procesu nawiązywania połączenia przy każdym zapytaniu do bazy danych.

**Leniwe inicjalizowanie** (lazy initialization) oznacza, że połączenie z bazą danych jest tworzone dopiero w momencie, gdy jest rzeczywiście potrzebne, a nie podczas ładowania klasy. Metoda `getPdo()` sprawdza, czy połączenie już istnieje, i tworzy je tylko wtedy, gdy jest to konieczne. Dodatkowo, konfiguracja (parametry połączenia) jest ładowana również leniwie, dopiero przy pierwszym użyciu, co przyspiesza start aplikacji oraz umożliwia łatwe testowanie bez konieczności konfiguracji bazy danych.

**Centralna konfiguracja** odbywa się przez zmienne środowiskowe, które są odczytywane przez klasę `Env`. Parametry połączenia (host, port, nazwa bazy, użytkownik, hasło) są przechowywane w pliku `.env`, który nie jest commitowany do repozytorium, co zapewnia bezpieczeństwo wrażliwych danych. Klasa `Database` odczytuje te parametry przy pierwszym połączeniu i wykorzystuje je do utworzenia DSN (Data Source Name) dla PDO. Taka konfiguracja umożliwia łatwe przełączanie między różnymi środowiskami (deweloperskie, testowe, produkcyjne) bez modyfikacji kodu.

### 6.3 Response Helper

Klasa `Response` standaryzuje odpowiedzi API, zapewniając spójność oraz eliminując duplikację kodu związanego z wysyłaniem odpowiedzi HTTP. Klasa zawiera wszystkie standardowe kody statusu HTTP jako stałe, co zapewnia type safety oraz zapobiega błędom w kodach statusu.

**Spójny format JSON** jest zapewniony przez metodę `json()`, która automatycznie ustawia nagłówek `Content-Type: application/json; charset=utf-8` oraz koduje dane do formatu JSON przy użyciu funkcji `json_encode()`. Metoda przyjmuje dane oraz opcjonalny kod statusu HTTP, co umożliwia zwracanie różnych kodów (200 dla sukcesu, 201 dla utworzenia zasobu, etc.) w spójny sposób. Metoda kończy wykonanie skryptu przez `exit`, co zapewnia, że żadne dodatkowe dane nie zostaną wysłane po odpowiedzi.

**Poprawne kody statusu HTTP** są ustawiane przez metodę `http_response_code()`, która jest wywoływana przed wysłaniem odpowiedzi. Klasa zawiera wszystkie standardowe kody HTTP jako stałe (od 100 do 511), co umożliwia ich użycie w sposób czytelny i bezpieczny typowo. Na przykład, zamiast używać magicznych liczb jak `404`, można użyć `Response::HTTP_NOT_FOUND`, co jest bardziej czytelne i mniej podatne na błędy.

**Centralne zarządzanie nagłówkami** zapewnia, że wszystkie odpowiedzi API mają spójne nagłówki, co jest ważne dla kompatybilności z różnymi klientami oraz dla bezpieczeństwa. Metoda `error()` jest wygodnym skrótem do zwracania błędów, przyjmując komunikat oraz kod statusu i automatycznie formatując odpowiedź w standardowym formacie `{ "message": "..." }`. Taka standaryzacja ułatwia obsługę błędów po stronie klienta oraz zapewnia spójne doświadczenie użytkownika.

### 6.4 Stałe i Konfiguracja

Klasa `Consts` centralizuje wartości stałe używane w całej aplikacji, takie jak statusy ofert, typy paliw, typy skrzyń biegów, typy nadwozia oraz listy krajów. Centralizacja tych wartości jest kluczowa dla utrzymania spójności oraz ułatwia zarządzanie zmianami.

Statusy ofert są zdefiniowane jako stałe (`OFFER_STATUS_ACTIVE`, `OFFER_STATUS_SOLD`, `OFFER_STATUS_REMOVED`), co zapewnia, że te same wartości są używane w całej aplikacji. Listy wartości enum (typy paliw, skrzynie biegów, etc.) są zwracane przez metody statyczne (`getFuelTypes()`, `getTransmissionTypes()`, etc.), co umożliwia łatwe dodawanie nowych wartości oraz zapewnia, że wszystkie części aplikacji używają aktualnej listy.

Taka organizacja ułatwia zarządzanie, ponieważ zmiana listy dostępnych wartości wymaga modyfikacji tylko jednego miejsca w kodzie. Dodatkowo, centralizacja zapewnia spójność walidacji - endpointy używają tych samych list do weryfikacji danych wejściowych, co eliminuje ryzyko niespójności między różnymi częściami aplikacji. Klasa `Consts` jest również łatwa do testowania oraz może być rozszerzona o dodatkowe funkcjonalności, takie jak walidacja wartości czy tłumaczenia.

## 7. Deployment i Środowisko

### 7.1 Docker

Aplikacja została skonteneryzowana przy użyciu Docker Compose, co umożliwia łatwe zarządzanie całym środowiskiem deweloperskim oraz zapewnia spójność między różnymi maszynami. Docker Compose koordynuje działanie wielu kontenerów, definiując ich konfigurację, zależności oraz sieci w jednym pliku `docker-compose.yml`.

Kontener **backend** jest oparty na obrazie `php:8.4-apache`, który zawiera najnowszą wersję PHP wraz z serwerem Apache. Kontener został skonfigurowany z rozszerzeniem `pdo_mysql` do komunikacji z bazą danych oraz z włączonym modułem `mod_rewrite` Apache. Dodatkowo, kontener ma skonfigurowane limity uploadu plików (10MB) oraz lokalizację polską (pl_PL.UTF-8) i strefę czasową (Europe/Warsaw). Kontener **db** wykorzystuje oficjalny obraz `mysql:8.4` z prekonfigurowaną bazą danych `szrotomoto` oraz użytkownikiem aplikacji. Baza danych jest przechowywana w wolumenie Docker, co zapewnia trwałość danych między restartami kontenerów.

Kontener **frontend** jest osobnym serwisem, co umożliwia niezależne skalowanie oraz zarządzanie frontendem i backendem. Kontenery komunikują się przez dedykowane sieci Docker: sieć `private` łączy backend z bazą danych, zapewniając bezpieczną komunikację bez ekspozycji na zewnętrzne interfejsy, podczas gdy sieć `public` umożliwia komunikację między frontendem a backendem. Taka architektura sieci zapewnia izolację oraz bezpieczeństwo, ponieważ baza danych nie jest bezpośrednio dostępna z zewnątrz kontenerów.

Docker Compose automatycznie zarządza zależnościami między kontenerami (`depends_on`), zapewniając, że baza danych jest gotowa przed uruchomieniem backendu, a backend jest gotowy przed uruchomieniem frontendu. Healthcheck dla bazy danych dodatkowo weryfikuje, czy MySQL jest gotowy do przyjmowania połączeń, co zwiększa niezawodność uruchamiania aplikacji.

### 7.2 Konfiguracja

Konfiguracja aplikacji odbywa się przez plik `.env`, który jest standardowym mechanizmem zarządzania konfiguracją w aplikacjach webowych. Plik `.env` nie jest commitowany do repozytorium (jest w `.gitignore`), co zapewnia bezpieczeństwo wrażliwych danych oraz umożliwia różne konfiguracje dla różnych środowisk.

Plik `.env` zawiera **parametry połączenia z bazą danych**, w tym host (`DB_HOST`), port (`DB_PORT`), nazwę bazy danych (`DB_NAME`), użytkownika (`DB_USER`) oraz hasło (`DB_PASSWORD`). Te parametry są odczytywane przez klasę `Env` przy użyciu funkcji `getenv()`, która pobiera zmienne środowiskowe. W kontekście Docker, plik `.env` jest montowany jako read-only volume w kontenerach, co zapewnia, że zmiany w konfiguracji nie wymagają przebudowy obrazów.

**Ustawienia lokalizacji i strefy czasowej** są konfigurowane zarówno w pliku `.env`, jak i bezpośrednio w kontenerach Docker. Kontenery mają ustawione zmienne środowiskowe `LANG`, `LANGUAGE`, `LC_ALL` oraz `TZ`, co zapewnia spójne formatowanie dat, liczb oraz komunikatów błędów w całej aplikacji. Polska lokalizacja jest szczególnie ważna dla aplikacji skierowanej do polskich użytkowników, ponieważ zapewnia poprawne formatowanie dat oraz komunikatów.

**Inne zmienne środowiskowe** mogą być łatwo dodane do pliku `.env` i wykorzystane w aplikacji przez klasę `Env`. Taki mechanizm umożliwia elastyczną konfigurację bez konieczności modyfikacji kodu, co jest szczególnie przydatne przy wdrażaniu na różnych środowiskach (deweloperskie, testowe, produkcyjne).

### 7.3 Migracje i Seeding

System migracji umożliwia łatwe wdrażanie zmian w schemacie bazy danych oraz zapewnia kontrolę wersji struktury bazy danych. Skrypt `backend/bin/migrate.php` odczytuje wszystkie pliki SQL z katalogu `migrations/`, sortuje je chronologicznie na podstawie timestampu w nazwie pliku, a następnie wykonuje tylko te migracje, które jeszcze nie zostały zastosowane.

Mechanizm śledzenia wykonanych migracji działa poprzez tabelę `migrations` w bazie danych, która przechowuje nazwy wykonanych plików migracyjnych. Przed wykonaniem każdej migracji, skrypt sprawdza, czy dana migracja już została zastosowana, co zapobiega wielokrotnemu wykonaniu tej samej migracji. Taki mechanizm jest szczególnie ważny w środowisku zespołowym, gdzie różni deweloperzy mogą mieć różne wersje schematu bazy danych.

Dodatkowo dostępny jest seeder (`backend/bin/seed.php`) do wstępnego załadowania danych testowych, takich jak przykładowe marki, modele oraz użytkownicy. Seeder jest przydatny podczas rozwoju aplikacji, ponieważ umożliwia szybkie przygotowanie środowiska testowego z przykładowymi danymi. W przeciwieństwie do migracji, seeder może być uruchamiany wielokrotnie, co umożliwia resetowanie danych testowych do stanu początkowego. Seeder wykorzystuje te same mechanizmy dostępu do bazy danych co reszta aplikacji, co zapewnia spójność oraz umożliwia łatwe dodawanie nowych danych testowych.

## 8. Podsumowanie

Backend projektu Szrotomoto został zaprojektowany z naciskiem na jakość kodu, bezpieczeństwo oraz utrzymywalność. Projekt demonstruje zastosowanie dobrych praktyk programistycznych oraz wzorców projektowych w kontekście aplikacji webowej.

**Czytelność kodu** została osiągnięta poprzez jasną strukturę projektu z wyraźnym podziałem na warstwy oraz logiczną organizacją plików. Separacja odpowiedzialności między warstwami (API, logika biznesowa, dostęp do danych, prezentacja) ułatwia zrozumienie systemu oraz umożliwia równoległą pracę nad różnymi częściami aplikacji. Użycie znaczących nazw klas, metod oraz zmiennych dodatkowo poprawia czytelność kodu.

**Bezpieczeństwo** jest zapewnione na wielu poziomach: walidacja danych wejściowych zapobiega wprowadzaniu błędnych lub złośliwych danych, autoryzacja kontroluje dostęp do zasobów, parametryzowane zapytania chronią przed SQL injection, a bezpieczne przechowywanie haseł (hashe) oraz sesji zapewnia ochronę danych użytkowników. System uploadu plików zawiera wielowarstwową walidację, co zapobiega uploadowi niebezpiecznych plików.

**Skalowalność** została uwzględniona w architekturze systemu poprzez modularną strukturę oraz elastyczne API. Dodawanie nowych funkcjonalności nie wymaga modyfikacji istniejącego kodu, a jedynie dodania nowych endpointów lub rozszerzenia istniejących klas. Query Builder oraz centralne klasy pomocnicze ułatwiają rozwijanie funkcjonalności bez duplikacji kodu.

**Utrzymywalność** jest wspierana przez wykorzystanie wzorców projektowych (Singleton, Fluent Interface) oraz dobrych praktyk programistycznych. System migracji umożliwia kontrolowane zmiany w schemacie bazy danych, a centralizacja stałych oraz konfiguracji ułatwia zarządzanie aplikacją. Kod jest zorganizowany w sposób, który ułatwia testowanie oraz refaktoryzację.

**Dokumentacja** została przygotowana w formacie Markdown, co zapewnia łatwość czytania oraz możliwość automatycznego generowania dokumentacji. Dokumentacja API (`ENDPOINTS.md`) zawiera szczegółowe opisy wszystkich endpointów wraz z przykładami żądań i odpowiedzi, co ułatwia integrację oraz rozwój aplikacji.

System spełnia wymagania funkcjonalne aplikacji do zarządzania ofertami pojazdów, zapewniając przy tym solidne fundamenty do dalszego rozwoju. Architektura oraz implementacja są gotowe na rozszerzenie o dodatkowe funkcjonalności, takie jak wyszukiwanie zaawansowane, powiadomienia, system ocen użytkowników czy integracja z zewnętrznymi serwisami.

