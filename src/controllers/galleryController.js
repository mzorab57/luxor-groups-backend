const express = require("express");
const router = express.Router();
const Gallery = require("../models/gallery");
const multer = require("multer");
const path = require("path");

// Configure storage
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, "uploads/"); // Make sure this folder exists
  },
  filename: function (req, file, cb) {
    cb(null, Date.now() + "-" + file.originalname);
  },
});
const upload = multer({ storage: storage });

const galleryController = {
  // Create new gallery
  async create(req, res) {
    try {
      const imagePaths = req.files ? req.files.map(file => '/uploads/' + file.filename) : [];
      const {
        qr_code,
        title,
        description,
        category,
        size,
        price,
        sku,
        orientation,
        artist_name,
        upload_date,
      } = req.body;
      const data = {
        images: imagePaths,
        qr_code,
        title,
        description,
        category,
        size,
        price,
        sku,
        orientation,
        artist_name,
        upload_date,
      };
      console.log("Gallery data to save:", data); // <--- Add this line
      const galleryItem = await Gallery.create(data);
      res.status(201).json(galleryItem);
    } catch (err) {
      console.error("Gallery create error:", err); // <--- Add this line
      res.status(500).json({ error: err.message });
    }
  },

  // Get all gallerys
  async getAll(req, res) {
    try {
      const gallerys = await Gallery.getAll();
      res.json(gallerys);
      console.log(gallerys);
      console.log("gallerys");
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  },

  // Get gallery by ID
  async getById(req, res) {
    try {
      const { id } = req.params;
      const galleryItem = await Gallery.getById(id);
      if (!galleryItem)
        return res.status(404).json({ error: "gallery not found" });
      res.json(galleryItem);
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  },

  // Update gallery by ID
  async update(req, res) {
    try {
      const { id } = req.params;
      const data = req.body;
      const updated = await Gallery.update(id, data);
      res.json(updated);
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  },

  // Delete gallery by ID
  async delete(req, res) {
    try {
      const { id } = req.params;
      console.log(id);

      await Gallery.delete(id);
      res.json({ message: "gallery deleted", id });
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  },
};

module.exports = galleryController;

// In your route file
router.post("/api/gallery", upload.array("images", 10), async (req, res) => {
  try {
    const imagePaths = req.files.map((file) => "/uploads/" + file.filename);
    // Get other fields from req.body
    const {
      qr_code,
      title,
      description,
      category,
      size,
      price,
      sku,
      orientation,
      artist_name,
      upload_date,
    } = req.body;
    // Save to DB (adjust to your model)
    const newGallery = await Gallery.create({
      images: imagePaths,
      qr_code,
      title,
      description,
      category,
      size,
      price,
      sku,
      orientation,
      artist_name,
      upload_date,
    });
    res.status(201).json(newGallery);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});
