# phpSymfony

PHP Symfony keretrenfszerben készült projektmunkám, melyet családi vállalkozásunknak készítettem. Ezért a weboldalon az arra feltöltött újságokat leht megtekinteni, melyet külön szoftver készít el.

A projektet letöltve elindítva feltöltés előtt nem teszteltem, jelenleg előben fut a következő linken: https://miujsag.dunafenye.hu/

Sajnos szerver alapú rendereléssel van csak elátva, így a kliens az lényegesen sokat várhat a részletek megjelenítésére, melyeket javascript kérésekkel vannak megvalósítva.

A weboldal 4 fő oldalból áll melyek:
 * főoldal
 * fórum
 * megjelenések
 * hirdetési kérelem
Ezen felül bejelentkezés után minden felhasználónak létezik adatlapja melyet szerkeszthet ls nyilvánosan látható, és az admin felhasználóknak is látható egy külön kezelői felület.

## Kezdőlap/Főoldal:
Itt jelenik meg a legutolsó 2 megjelent újság
![image](https://user-images.githubusercontent.com/66252236/140048373-5db9268d-6eae-4a74-a579-597400484597.png)


## Fórum
Beszélgetési lehetőséggel ellátott felület amelyre minden felhasználó létrehozhat egy új fórumot és azokhozz külön szövegesen hozzászólhat. Admin felhasználóként ki tudjuk választani, hogybaz adott fórum publikusan látható is legyen e, vagy nem.
![image](https://user-images.githubusercontent.com/66252236/140048429-c4e8b715-36eb-40b7-ae0b-047fa4954f2a.png)

Fórumot létrehozva abban a beszélgetés javascript frissítéssel van megoldva. Ez folyamatosan kérdezgeti a szervert, hogy mi a legutolsó üzenet id-je és aszerint lekéri csak az utolsó x db-ot amennyiben érkezett. Ez a beszélgetésben egy 1-2mp késést jelenthet míg megjeleni és most nem így csinálnám meg, de az Angulart csak ezek után később tudtam elkezdeni gyakorolni.
![image](https://user-images.githubusercontent.com/66252236/140048913-f55f0b69-7377-4f7e-9706-35f95a6ce0c2.png)

## Megjelenések
Ezen az oldalon jelenleg csak listázásra kerül az eddigi összes adatbázisban rögzített megjelenés, és ezekne a választott vagy index képe.
![image](https://user-images.githubusercontent.com/66252236/140049081-3284e3a9-2914-4bfc-aef9-d157ff6bf180.png)

## Hirdetés feladás
Utolsó publikus oldal még a hirdetés feladás ahol választhatunk, hogy milyen hirdetésre szeretnénk ajánlatot kérni az újságban.
![image](https://user-images.githubusercontent.com/66252236/140049278-a480550d-3410-4a1a-9da4-a174b3dd0651.png)

## Adatlap
Ezen az oldalon megjeleníthetjük adatainkat amiből kiválaszthatjuk, hogy mit szeretnénk, hogy többen is lássanak.
### Egyénileg:
![image](https://user-images.githubusercontent.com/66252236/140049550-a6d04df0-e19b-4fa9-b6e1-623f4799dcab.png)
### Publikusan:
![image](https://user-images.githubusercontent.com/66252236/140049591-064ec568-9a75-462b-9552-da96d3bbece5.png)
### Módosítás:
![image](https://user-images.githubusercontent.com/66252236/140049719-4e9768ec-0e78-4c17-a50f-d1ce0eaffe5f.png)


## Admin és egyéb funkciók:

Az oldalon továbbá tudunk elfelejtett jelszót is cserélni amely tokennel van megvalósítva amelyet emailben megkapunk ha kérjük.

Admin felületen látjuk a felhasználókat egyből ugyanazon a lapon javascriptes lazy loading megvalósítással.
![image](https://user-images.githubusercontent.com/66252236/140050023-0df264b5-6ff8-4be4-9211-0f180f1078d0.png)

Ugyanilyen megvalósítással láthatók a fórumok, újságok és hirdetési kérelmek is, melyekhez a feltöltött dokumentumokat egy .zip fájlként le is tudunk tölteni.
Itt továbbá megjeleni az is jelvényként, hogy hány új hirdetési kérelem van és, hogy láttuk e már vagy még nem.
![image](https://user-images.githubusercontent.com/66252236/140050318-7453438a-9fdf-471b-899e-3d5163cc6779.png)

Legutoljára körlevelet is lehet küldeni, amelyet egy word dokumentumból html-ben kimentve, vagy szöveges állományban feltöltve lehet  elküldeni
![image](https://user-images.githubusercontent.com/66252236/140050476-421e20a6-e17e-4db8-8586-f008b26619bf.png)




