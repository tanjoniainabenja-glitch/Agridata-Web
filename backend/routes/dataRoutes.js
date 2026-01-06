const express = require('express');
const router = express.Router();
const Data = require('../models/Data');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Configuration de multer pour l'upload d'images
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const uploadDir = 'uploads/images';
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({ 
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // 5MB max
    fileFilter: (req, file, cb) => {
        const allowedTypes = /jpeg|jpg|png|gif/;
        const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
        const mimetype = allowedTypes.test(file.mimetype);
        
        if (mimetype && extname) {
            return cb(null, true);
        } else {
            cb(new Error('Seules les images sont autorisées'));
        }
    }
});

// GET - Récupérer toutes les données
router.get('/data', async (req, res) => {
    try {
        const { 
            category, 
            liked, 
            search, 
            sortBy = 'createdAt', 
            order = 'desc',
            page = 1,
            limit = 10 
        } = req.query;
        
        // Construire le filtre
        let filter = {};
        if (category) filter.category = category;
        if (liked !== undefined) filter.liked = liked === 'true';
        if (search) {
            filter.$text = { $search: search };
        }
        
        // Pagination
        const skip = (page - 1) * limit;
        
        // Requête
        const data = await Data.find(filter)
            .sort({ [sortBy]: order === 'desc' ? -1 : 1 })
            .skip(skip)
            .limit(parseInt(limit));
            
        const total = await Data.countDocuments(filter);
        
        res.json({
            success: true,
            data: data,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: total,
                pages: Math.ceil(total / limit)
            }
        });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la récupération des données', 
            error: error.message 
        });
    }
});

// GET - Récupérer une donnée par ID
router.get('/data/:id', async (req, res) => {
    try {
        const data = await Data.findById(req.params.id);
        
        if (!data) {
            return res.status(404).json({ 
                success: false, 
                message: 'Donnée non trouvée' 
            });
        }
        
        // Incrémenter le nombre de vues
        data.views += 1;
        await data.save();
        
        res.json({ success: true, data: data });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la récupération', 
            error: error.message 
        });
    }
});

// POST - Créer une nouvelle donnée
router.post('/data', upload.single('image'), async (req, res) => {
    try {
        const { name, description, category, metadata, location, userId } = req.body;
        
        let imageUrl = req.body.imageUrl;
        if (req.file) {
            imageUrl = `${req.protocol}://${req.get('host')}/uploads/images/${req.file.filename}`;
        }
        
        const newData = new Data({
            name,
            description,
            imageUrl,
            category,
            metadata: metadata ? JSON.parse(metadata) : undefined,
            location: location ? JSON.parse(location) : undefined,
            userId
        });
        
        const savedData = await newData.save();
        
        res.status(201).json({ 
            success: true, 
            message: 'Donnée créée avec succès', 
            data: savedData 
        });
    } catch (error) {
        res.status(400).json({ 
            success: false, 
            message: 'Erreur lors de la création', 
            error: error.message 
        });
    }
});

// PUT - Mettre à jour une donnée
router.put('/data/:id', upload.single('image'), async (req, res) => {
    try {
        const updateData = { ...req.body };
        
        if (req.file) {
            updateData.imageUrl = `${req.protocol}://${req.get('host')}/uploads/images/${req.file.filename}`;
        }
        
        if (updateData.metadata) {
            updateData.metadata = JSON.parse(updateData.metadata);
        }
        
        if (updateData.location) {
            updateData.location = JSON.parse(updateData.location);
        }
        
        updateData.updatedAt = Date.now();
        
        const data = await Data.findByIdAndUpdate(
            req.params.id, 
            updateData, 
            { new: true, runValidators: true }
        );
        
        if (!data) {
            return res.status(404).json({ 
                success: false, 
                message: 'Donnée non trouvée' 
            });
        }
        
        res.json({ 
            success: true, 
            message: 'Donnée mise à jour avec succès', 
            data: data 
        });
    } catch (error) {
        res.status(400).json({ 
            success: false, 
            message: 'Erreur lors de la mise à jour', 
            error: error.message 
        });
    }
});

// PATCH - Mettre à jour le statut liked
router.patch('/data/:id/like', async (req, res) => {
    try {
        const data = await Data.findById(req.params.id);
        
        if (!data) {
            return res.status(404).json({ 
                success: false, 
                message: 'Donnée non trouvée' 
            });
        }
        
        data.liked = !data.liked;
        await data.save();
        
        res.json({ 
            success: true, 
            message: 'Statut like mis à jour', 
            data: data 
        });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la mise à jour du like', 
            error: error.message 
        });
    }
});

// DELETE - Supprimer une donnée
router.delete('/data/:id', async (req, res) => {
    try {
        const data = await Data.findByIdAndDelete(req.params.id);
        
        if (!data) {
            return res.status(404).json({ 
                success: false, 
                message: 'Donnée non trouvée' 
            });
        }
        
        res.json({ 
            success: true, 
            message: 'Donnée supprimée avec succès' 
        });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la suppression', 
            error: error.message 
        });
    }
});

// GET - Statistiques
router.get('/stats/overview', async (req, res) => {
    try {
        const totalData = await Data.countDocuments();
        const likedData = await Data.countDocuments({ liked: true });
        const categories = await Data.distinct('category');
        const recentData = await Data.find().sort({ createdAt: -1 }).limit(5);
        
        res.json({
            success: true,
            stats: {
                total: totalData,
                liked: likedData,
                categories: categories.length,
                recentData: recentData
            }
        });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la récupération des statistiques', 
            error: error.message 
        });
    }
});

// POST - Synchronisation batch
router.post('/data/sync', async (req, res) => {
    try {
        const { data } = req.body;
        
        if (!Array.isArray(data)) {
            return res.status(400).json({ 
                success: false, 
                message: 'Les données doivent être un tableau' 
            });
        }
        
        const results = [];
        
        for (const item of data) {
            if (item._id) {
                // Mise à jour si l'ID existe
                const updated = await Data.findByIdAndUpdate(
                    item._id, 
                    item, 
                    { new: true, upsert: true }
                );
                results.push(updated);
            } else {
                // Création si pas d'ID
                const created = new Data(item);
                const saved = await created.save();
                results.push(saved);
            }
        }
        
        res.json({ 
            success: true, 
            message: 'Synchronisation réussie', 
            data: results 
        });
    } catch (error) {
        res.status(500).json({ 
            success: false, 
            message: 'Erreur lors de la synchronisation', 
            error: error.message 
        });
    }
});

module.exports = router;