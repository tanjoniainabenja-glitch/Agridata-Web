const mongoose = require('mongoose');

const connectDB = async () => {
    try {
        // Connexion sans les options dépréciées
        const conn = await mongoose.connect(
            process.env.MONGODB_URI || 'mongodb://localhost:27017/agridata'
        );
        
        console.log(`✅ MongoDB connecté: ${conn.connection.host}`);
        
        // Initialiser les données par défaut
        await initializeDefaultData();
    } catch (error) {
        console.error('❌ Erreur MongoDB:', error.message);
        console.log('⚠️  Continuant sans base de données...');
    }
};

const initializeDefaultData = async () => {
    try {
        const Data = require('../models/Data');
        const count = await Data.countDocuments();
        
        if (count === 0) {
            console.log('📝 Initialisation des données...');
            const defaultData = [];
            
            await Data.insertMany(defaultData);
            console.log('✅ Données insérées');
        } else {
            console.log(`📊 ${count} données existantes`);
        }
    } catch (error) {
        console.log('⚠️  Mode sans DB:', error.message);
    }
};

module.exports = connectDB;