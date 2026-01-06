const { MongoClient } = require("mongodb");
const uri = "mongodb://127.0.0.1:27017";
const dbName = "minae";

const regions = ["Alaotra-Mangoro","Vakinankaratra","Atsinanana","Atsimo-Andrefana"];
const cultures = ["riz","maïs","manioc","café"];
const annees = [2022, 2023, 2024];

function rand(min, max) { return Math.round(min + Math.random() * (max - min)); }

(async () => {
  const client = new MongoClient(uri);
  await client.connect();
  const db = client.db(dbName);

  // Admin
  const bcrypt = require("bcrypt");
  const passwordHash = await bcrypt.hash("Admin123!", 10);
  await db.collection("user").deleteMany({});
  await db.collection("user").insertOne({
    username: "admin",
    fullname: "Admin AgriData",
    email: "admin@eminae.mg",
    password: passwordHash,
    created_at: new Date(),
  });

  // Datasets catalogue
  await db.collection("datasets").deleteMany({});
  const datasets = [
    { code: "prod_riz", titre: "Production riz 2022-2024", theme: "cultures", couverture: "région,district", maj: "annuelle" },
    { code: "prod_mais", titre: "Production maïs 2022-2024", theme: "cultures", couverture: "région,district", maj: "annuelle" },
    { code: "cheptel_bovin", titre: "Cheptel bovin 2023-2024", theme: "élevage", couverture: "région", maj: "annuelle" }
  ];
  await db.collection("datasets").insertMany(datasets);

  // Stats production
  await db.collection("stats_production").deleteMany({});
  const prodDocs = [];
  for (const annee of annees) {
    for (const region of regions) {
      for (const culture of cultures) {
        const superficie = rand(20000, 200000);
        const rendement = (Math.random() * 2 + 3.0).toFixed(1); // 3.0 à 5.0
        const production = Math.round(superficie * rendement);
        prodDocs.push({
          annee,
          region,
          culture,
          superficie_ha: superficie,
          production_t: production,
          rendement_t_ha: parseFloat(rendement),
          source: annee >= 2024 ? "EAA 2024 (provisoire)" : "EAA " + annee,
          maj: new Date()
        });
      }
    }
  }
  await db.collection("stats_production").insertMany(prodDocs);

  // Stats cheptel
  await db.collection("stats_cheptel").deleteMany({});
  const especes = ["bovin","ovin","caprin","volaille"];
  const cheptelDocs = [];
  for (const annee of [2023, 2024]) {
    for (const region of regions) {
      for (const espece of especes) {
        cheptelDocs.push({
          annee,
          region,
          espece,
          effectif_tetes: rand(50000, 500000),
          source: "Direction de l'Élevage",
          maj: new Date()
        });
      }
    }
  }
  await db.collection("stats_cheptel").insertMany(cheptelDocs);

  console.log("Seed terminé.");
  await client.close();
})();
