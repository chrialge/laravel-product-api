# Instruction

<aside>
📚 esercizio di oggi: Laravel Product API
nome repo: **laravel-product-api

🎯 Obiettivi\*\*
Dovrai realizzare un progetto Laravel per sviluppare un’API che gestisca prodotti e categorie.

**I dati dei prodotti** dovranno essere generati con tramite **Seeder** con **Faker**. Genera almeno 100 prodotti fittizi.

---

### Milestone

**1️⃣ Scaffold di progetto**
Realizza un nuovo progetto Laravel. Completa lo scaffolding e la procedura di inizializzazione di un nuovo progetto.

**2️⃣ Migration**
Realizza le migration per le risorse. Non dimenticare: la categoria sarà necessariamente associata ad un prodotto. Ogni prodotto sarà disporrà almeno dei seguenti attributi: **id, nome, descrizione, prezzo.**

**3️⃣ Modelli, relazioni e seeder**
Realizza i modelli per le risorse. Non dimenticare di rappresentare la relazione esistente tra prodotti e categorie. Realizza poi i seeder per le risorse.

4️⃣ **API CRUD**

Crea i **controller** per la risorsa Product.

```
# Restituisce tutti i prodotti
GET /api/products

# Restituisce i dettagli di un prodotto
GET /api/products/{id}

# Crea un nuovo prodotto
POST /api/products

# Aggiorna un prodotto esistente
PUT /api/products/{id}

# Elimina un prodotto
DELETE /api/products/{id}
```

⭐ **Bonus: filtra i prodotti per categoria e quelli “in evidenza”**

Effettua le opportune modifiche per permettere il filtraggio dei prodotti per categoria e per l’attributo “in evidenza”.

</aside>
