const mongoose = require('mongoose');

const DataSchema = new mongoose.Schema({
    name: {
        type: String,
        required: true,
        trim: true
    },
    description: {
        type: String,
        required: true,
        trim: true
    },
    imageUrl: {
        type: String,
        required: true
    },
    category: {
        type: String,
        default: 'Non catégorisé'
    },
    liked: {
        type: Boolean,
        default: false
    },
    views: {
        type: Number,
        default: 0
    },
    location: {
        latitude: {
            type: Number,
            default: null
        },
        longitude: {
            type: Number,
            default: null
        }
    },
    metadata: {
        temperature: Number,
        humidity: Number,
        soilPH: Number,
        lastWatered: Date
    },
    userId: {
        type: String,
        default: null
    },
    createdAt: {
        type: Date,
        default: Date.now
    },
    updatedAt: {
        type: Date,
        default: Date.now
    }
}, {
    timestamps: true
});

// Index pour améliorer les performances de recherche
DataSchema.index({ name: 'text', description: 'text' });
DataSchema.index({ category: 1 });
DataSchema.index({ liked: 1 });
DataSchema.index({ createdAt: -1 });

module.exports = mongoose.model('Data', DataSchema);