require('dotenv').config();
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');
const connectDB = require('./config/database');
const dataRoutes = require('./routes/dataRoutes');

const app = express();
const PORT = process.env.PORT || 3000;

// Connexion à MongoDB
connectDB();

// Middleware
app.use(cors({
    origin: '*', // En production, spécifiez les origines autorisées
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
    allowedHeaders: ['Content-Type', 'Authorization']
}));

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Servir les fichiers statiques (images uploadées)
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Routes
app.use('/api', dataRoutes);

// Route de base
app.get('/', (req, res) => {
    res.json({ 
        message: 'API AgriData Backend', 
        version: '1.0.0',
        endpoints: {
            getAllData: 'GET /api/data',
            getDataById: 'GET /api/data/:id',
            createData: 'POST /api/data',
            updateData: 'PUT /api/data/:id',
            toggleLike: 'PATCH /api/data/:id/like',
            deleteData: 'DELETE /api/data/:id',
            getStats: 'GET /api/stats/overview',
            syncData: 'POST /api/data/sync'
        }
    });
});

// Gestion des erreurs 404
app.use((req, res) => {
    res.status(404).json({ 
        success: false, 
        message: 'Route non trouvée' 
    });
});

// Gestion globale des erreurs
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ 
        success: false, 
        message: 'Erreur serveur', 
        error: process.env.NODE_ENV === 'development' ? err.message : 'Erreur interne'
    });
});

// Démarrage du serveur
app.listen(PORT, () => {
    console.log(`Serveur démarré sur le port ${PORT}`);
    console.log(`API disponible sur http://localhost:${PORT}/api`);
});