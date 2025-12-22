# projekt_szrotomoto

## Instalacja na Windows
1. Zainstaluj Docker Desktop [pobierz](https://www.docker.com/products/docker-desktop/)
2. Zainstaluj dowolny WSL2 (np. Ubuntu)
3. W Docker Desktop:
    - Ustawienia
    - Resources
    - WSL integration
    - Włącz integrację oraz włącz dla twojej dystrybucji WSL
4. W VSCode lewy dolny, "Connect to WSL", wybieramy dystrybucję
5. W terminalu w VSCode:
```
git clone git@github.com:23vbq/projekt_szrotomoto.git
cd projekt_szrotomoto
```
6. W VSCode `CTRL+K CTRL+O`, wybieramy folder `projekt_szrotomoto`
7. W terminalu 
```
docker-compose up -d --build
```

## Uruchomienie na windows
1. Uruchom Docker Desktop
2. Z poziomu WSL (np. w VSCode Remote Connection)
```
docker-compose up -d
```