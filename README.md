# Webstore
End work for Web programming, advanced course from my second school year. (2016)

### Santeri Aleksi Hetekivi Web-ohjelmoinnin jatkokurssi Harjoitustyö

### 1202466 13I224 5G00BM80- 3001 Single-page application web-kauppa

# Single-page application web-kauppa

## 1. APP

## Navigointi:

Sivustolla on oma navigointi manageri, joka sivulle siirryttäessä estää siirron tarkistaa REST rajapinnalta onko
käyttäjällä oikeutta kyseiseen sivuun ja jos on niin pistää talteen ja ohjaa käyttäjän sivulle.

## Käyttäjänhallinta:

Käyttäjät on jaettu neljään päätyyppiin ja vieraisiin. Adminilla on oikeus kaikkeen ohjelmassa, managerilla on kaikki
muut sallittuja paitsi käyttäjien muokkaus ja työntekijältä on estetty tietojen poistaminen. Asiakkailla on ainut oikeus
katsella Omaa Sivuaan missä näkyy vanhat tilaukset ja ostoskärryssä tiedot täyttyvät itsestään käyttäjän tiedoilla.

## Sivut

1. Kauppa
    Lajittelu tuotetyypin, sarakkeen tai hakutekstin mukaan onnistuu. Tuotteen ostaessa se siirtyy ostoskoriin
    jonka laskuri päivittyy.
2. Oma Sivu
    Kaikki rekisteröidyt käyttäjät voivat täältä katsella tilauksiensa ja saavat tiedon onko tilaus toimitettu ja jos on
    niin milloin.
3. Hallinta
    Tämä valikko on rajattu vain kaupan henkilöstölle, asiakkaat ja vieraat eivät edes näe sitä.
    Täältä käsin voidaan muuttaa kaikkia tietoja aina tuotteista käyttäjiin asti (riippuen käyttöoikeuksistasi).
4. Ostoskori
    Ostoskoria säilytetään REST rajapinnalla selain sessiossa. Tämän sivun kautta sitä pystyy manipuloimaan. Jos
    painaa MAKSA niin syntyy tilaus. En tehnyt tähän mitään oikeaa verkkopankki tai paypal maksu sovellusta
    vain tuon nappulan painaminen simuloi maksamista.
5. Kirjautuminen
    Viimeinen nappula navigaatio palkissa on kirjautumista varten. Se osaa muuttaa itsensä toiminnon
    uloskirjautumiseksi jos käyttäjä on jo sisällä. Sen kirjautumis näkymästä voi myös rekisteröidä itsellensä
    asiakas tilin, jonka salasana lähtee annettuun sähköpostiosoitteeseen.

## 2. REST

Rest rajapinnan toteutin PHP koodilla ja sisältää monta tuhatta riviä koodia ja älykkään tietokanta
keskustelurajapinnan.
Kutsut ovat tyyliä ”index.php/EDIT/User/1” Jossa EDIT on haluttava funktio, User on ensimmäinen parametri ja 1
toinen. Lisäparametrit annetaan POST tai GET tietoina rajapinnalle. Rajapinta palauttaa dataa json muodossa jossa itse
tiedot ovat data avaimen alla ja system avaimen alla on tietoja toiminnasta esimerkiksi tuliko virheitä yms.
REST rajapinnalla on oikea turvallisuus puoli, koska aina turvallisuus kannattaa rakentaa serverin eikä clientin puolelle.
Kukaan ei saa nähdä tai muokata tietoja mihin hänellä ei ole oikeuksia ja nykyinen käyttäjä ja koko rajapinta säilyy
sessiossa.

## 3. Lisäominaisuuksia:

1. Käyttäjähallinta
2. Ylläpitotoiminnot
3. Hakutoiminnot
4. Tuotelajit
5. Tilauksien lähetetyksi asetus ja sen näyttäminen asiakkaalle
6. Tietojen älykäs muotoilu ja esitys luokkien avulla
7. Modulaarinen rakenne, joka mahdollistaa laajentamisen vaikka minkälaiseksi kaupaksi.
8. Vahva tietoturva niin client kuin serveri puolella estää, muiden kuin henkilökunnan pääsyn hallintaan, näin
    sallien hallinnan sijaitsevan samassa näkymässä kaupan kanssa.
9. Ostoskori ja tiedot säilyvät tallessa niin kauan kunnes poistat sessionin tai suljet selaimen.
10. Virheiden hallinta toimii. Serveri ilmoittaa ja ohjelma toimii tietojen pohjalta.
11. Kaikki numeroarvot voidaan syöttää pilkun kanssa ja paljon muita käyttöä helpottavampia toimintoja.
12. Tietokannan tiedot vain deaktivoidaan palvelusta poistossa, joten jos henkilökunta tekee jotain väärin niin
    server admin voi aina palauttaa tiedot.
