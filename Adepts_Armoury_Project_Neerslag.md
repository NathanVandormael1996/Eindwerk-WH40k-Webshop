# Neerslag Project: Adept's Armoury

## 1. Projectomschrijving
*   **Titel van het project:** Adept's Armoury.
*   **Korte samenvatting:** Een multi-tenant e-commerce platform in een Warhammer 40K-thema. 
*   **Doelstelling:** Het platform is ontworpen om vanuit één centrale codebase meerdere onafhankelijke webshops te faciliteren. Elke webshop functioneert volledig autonoom op een eigen subdomein met een geïsoleerde database.
*   **Doelgroep:** Fans en verzamelaars van tabletop games (Warhammer 40K), die op zoek zijn naar specifieke categorieën zoals verven, miniaturen (figurines), videogames, bordspellen en comics.
*   **Probleemstelling of behoefte:** De noodzaak om efficiënt meerdere onafhankelijke winkels uit te rollen en te beheren (SaaS-model) zonder voor elke winkel een nieuwe applicatie te hoeven deployen.

## 2. Scope
*   **Wat werd gebouwd:** 
    *   Een dynamische storefront met Livewire-filtering.
    *   Een multi-tenancy architectuur (Stancl Tenancy v3) met gescheiden databases.
    *   Een centraal beheerderspaneel (`/dashboard`) voor producten, orders en categorieën.
    *   Een volledige Stripe checkout-flow inclusief automatische webhooks en terugbetalingen.
*   **Wat bewust niet werd gebouwd:** De winkelwagen wordt uitsluitend beheerd in de PHP-sessie (`$_SESSION['cart']`) en wordt niet opgeslagen in de database. Dit betekent dat verlaten winkelwagens niet over sessies heen bewaard blijven.
*   **Belangrijkste functionaliteiten:**
    *   Real-time productcatalogus met JSON-gebaseerde attributenfilters (bijv. filteren op 'faction' of 'color').
    *   Geavanceerd voorraadbeheer waarbij onderscheid wordt gemaakt tussen online voorraad (centraal magazijn) en fysieke winkellocaties.
    *   Automatische refund-integratie: wanneer een beheerder een order annuleert, communiceert de applicatie direct met de Stripe API om het bedrag terug te storten.

## 3. Analyse
*   **Functionele vereisten:** Bezoekers moeten kunnen zoeken en filteren zonder pagina-herlaad, veilig afrekenen via Stripe, en na aankoop reviews kunnen plaatsen.
*   **Niet-functionele vereisten:** Strikte data-isolatie tussen de verschillende webshops om privacy- en veiligheidsredenen.
*   **Gebruikersrollen:**
    *   **Customers:** Bestaan in de tenant-database, registreren per shop, en kunnen orders plaatsen en reviews schrijven.
    *   **Tenant Admins:** Klanten met de `is_admin` vlag in de tenant-database. Zij hebben toegang tot het dashboard om hun specifieke shop te beheren.
    *   **Central Admins:** Bestaan in de centrale database en beheren het overkoepelende platform (gescheiden van de tenant-gebruikers).

## 4. Technische uitwerking
*   **Gebruikte technologieën:** 
    *   Backend: PHP 8.3, Laravel 13.7.
    *   Frontend: Livewire 4.0, Livewire Flux 2.14, Tailwind CSS, Vite.
    *   Packages: `stancl/tenancy` v3.10 voor architectuur, `stripe/stripe-php` v20.2 voor betalingen.
*   **Projectstructuur (Multi-Tenancy):**
    Het project gebruikt een op domeinen gebaseerde tenant-identificatie. Bij een request identificeert de `InitializeTenancyByDomain` middleware het subdomein, zoekt deze op in de centrale database, en schakelt vervolgens de databaseconnectie over naar de specifieke tenant-database.
*   **Databankstructuur:**
    De architectuur is opgesplitst in een Centrale DB en meerdere Tenant DB's. Hieronder een weergave van de belangrijkste tabellen in de **Tenant Database**:

| Tabel `products` | Datatype | Beschrijving |
| :--- | :--- | :--- |
| `id` | BigInt | Primaire sleutel. |
| `category_id` | Foreign ID | Verwijzing naar categorie. |
| `slug` | String | URL-vriendelijke naam. |
| `price` | Integer | Prijs opgeslagen in centen ter voorkoming van afrondingsfouten. |
| `tags` | JSON | Array met labels zoals `["new", "sale"]`. |
| `attributes` | JSON | Dynamische attributen zoals `{"faction": "Space Marines"}`. |

| Tabel `orders` | Datatype | Beschrijving |
| :--- | :--- | :--- |
| `id` | BigInt | Primaire sleutel. |
| `user_id` | Foreign ID | Koper. |
| `total_amount` | Integer | Totaalbedrag in centen. |
| `status` | String | pending, paid, shipped, delivered, cancelled. |
| `stripe_session_id` | String | Koppeling met de Stripe Checkout sessie. |

| Tabel `product_stocks` | Datatype | Beschrijving |
| :--- | :--- | :--- |
| `product_id` | Foreign ID | Gekoppeld product. |
| `physical_store_id` | Foreign ID | Magazijn of winkel filiaal. |
| `quantity` | Integer | Beschikbare voorraad per locatie. |

*   **Belangrijke componenten:**
    *   `Livewire\Shop\Catalog`: Beheert de complexe, reactieve filterlogica via `JSON_CONTAINS` MySQL queries.
    *   `CheckoutController`: Wrapt het aanmaken van orders en order-items in een `DB::beginTransaction()`, roept vervolgens de Stripe API aan, en slaat pas op (`commit`) als Stripe succesvol een sessie retourneert.
    *   `StripeWebhookController`: Vangt de `checkout.session.completed` events op, verifieert de HMAC-handtekening van Stripe, en stelt de order status veilig in op `paid`.

## 5. Ontwerp en UX
*   **Kleurpalet & Thematiek:** Het design reflecteert de duistere sfeer van Warhammer 40K. Er wordt gebruik gemaakt van donkere achtergronden (`slate-950` en `slate-900`) met gouden accenten (`yellow-500` en `yellow-600`) voor primaire acties. Er ligt een subtiele gradiënt-overlay met een donkere textuur (`dark-matter.png`) over de body.
*   **UI Patronen:** Het systeem maakt zwaar gebruik van 'Glassmorphism' (bijv. `bg-slate-900/60 backdrop-blur-md`) om diepte te creëren. Buttons en kaarten maken gebruik van dynamische glow-effecten (schaduwen met transparant geel) die oplichten bij een hover-actie (`hover:shadow-[0_0_20px_rgba(234,179,8,0.5)]`).
*   **Typografie:** Headers gebruiken het gotische, schreeflettertype *Cinzel*, terwijl de algemene content wordt weergegeven in het schreefloze, moderne lettertype *Outfit* voor optimale leesbaarheid.
*   **Responsive en Toegankelijkheid:** Tailwind utility classes (zoals `flex-col-reverse lg:flex-row`) zorgen voor logische herschikkingen op mobiele apparaten (bijv. de winkelwagen-samenvatting boven het formulier tonen op kleine schermen). Verborgen filters gebruiken de `sr-only` class om toch leesbaar te blijven voor screenreaders.
*   **[ACTIE VEREIST: Voeg hier 2 of 3 screenshots toe van de storefront (catalogus) en het admin dashboard]**

## 6. Testing en kwaliteitscontrole
*   **Wat werd getest:** De webhook-afhandeling van Stripe is uitgebreid getest om te garanderen dat database-updates idempotent zijn (bijv. controleren of een status `pending` is voordat deze naar `paid` verandert). 
*   **[ACTIE VEREIST: Beschrijf hier hoe je dit hebt getest. Heb je de Stripe CLI gebruikt voor local webhook forwarding? Heb je handmatige tests uitgevoerd in de browser? Voeg eventueel een screenshot toe van een test-betaling in de Stripe testmodus.]**

## 7. Reflectie
*   **Wat goed ging:** **[ACTIE VEREIST: Vul in wat je succesvol vond gaan. Bijv. "Het integreren van het thema ging vlot dankzij Tailwind classes..."]**
*   **Wat moeilijk was:** **[ACTIE VEREIST: Vul in waar je tegenaan liep. Bijv. "Het werken met de gedeelde centrale en tenant-databases in Laravel via Stancl vereiste een stijle leercurve..."]**
*   **Wat de cursist geleerd heeft:** **[ACTIE VEREIST: Vul in. Bijv. "Ik heb geleerd hoe JSON-kolommen in MySQL gecombineerd met Livewire gebruikt kunnen worden om complexe filtersystemen op te bouwen..."]**
*   **Wat bij een volgende versie beter zou kunnen:** Het winkelwagensysteem draait momenteel in de sessie. In een toekomstige iteratie zou dit overgezet kunnen worden naar een database-model (bijv. een `carts` tabel) zodat klanten hun achtergelaten winkelwagen op een ander apparaat kunnen terughalen.

## 8. Bronnen en hulpmiddelen
*   **Libraries & Frameworks:** Laravel Framework, Livewire, Stancl Tenancy, Stripe PHP-SDK.
*   **Gebruikte Documentatie:** Officiële documentatie van Laravel (v13), Tailwind CSS, en Stripe API.
*   **AI-tools:** Qwen 2.5 Coder (via Continue CLI) en LLM's zijn gebruikt als pair-programming assistent en voor het genereren en structureren van deze projectdocumentatie op basis van de codebase.
*   **Design bronnen:** Bunny Fonts (Google Fonts alternatief voor Cinzel en Outfit) en TransparentTextures (dark-matter patroon).
