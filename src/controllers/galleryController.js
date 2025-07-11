const Gallery = require("../models/gallery");

const galleryController = {
  // Create new gallery
 async create(req, res) {
  try {
    console.log("Files received:", req.files);
    console.log("Body received:", req.body);
    
    let imagePaths = [];
    
    // Check if files are uploaded via form-data
    if (req.files && req.files.length > 0) {
      imagePaths = req.files.map(file => '/uploads/' + file.filename);
      console.log("Images from uploaded files:", imagePaths);
    }
    // Check if images are sent as URLs in the request body
    else if (req.body.images) {
      if (typeof req.body.images === 'string') {
        // Single image URL
        imagePaths = [req.body.images];
        console.log("Single image URL:", imagePaths);
      } else if (Array.isArray(req.body.images)) {
        // Array of image URLs
        imagePaths = req.body.images;
        console.log("Array of image URLs:", imagePaths);
      }
    }
    
    console.log("Final imagePaths:", imagePaths);
    
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
    
    console.log("Gallery data to save:", data);
    
    const galleryItem = await Gallery.create(data);
    res.status(201).json(galleryItem);
  } catch (err) {
    console.error("Gallery create error:", err);
    res.status(500).json({ error: err.message });
  }
},




  // Get all galleries
  async getAll(req, res) {
    try {
      const galleries = await Gallery.getAll();
      
      // Parse the images JSON string back to array for each gallery item
      const galleriesWithParsedImages = galleries.map(gallery => ({
        ...gallery,
        images: typeof gallery.images === 'string' ? JSON.parse(gallery.images) : gallery.images
      }));
      
      res.json(galleriesWithParsedImages);
      console.log("Galleries fetched:", galleriesWithParsedImages);
    } catch (err) {
      console.error("Get all galleries error:", err);
      res.status(500).json({ error: err.message });
    }
  },
  // Get gallery by ID
  async getById(req, res) {
    try {
      const { id } = req.params;
      const galleryItem = await Gallery.getById(id);
      
      if (!galleryItem) {
        return res.status(404).json({ error: "Gallery not found" });
      }
      
      // Parse the images JSON string back to array
      const galleryWithParsedImages = {
        ...galleryItem,
        images: typeof galleryItem.images === 'string' ? JSON.parse(galleryItem.images) : galleryItem.images
      };
      
      res.json(galleryWithParsedImages);
    } catch (err) {
      console.error("Get gallery by ID error:", err);
      res.status(500).json({ error: err.message });
    }
  },

  // Update gallery by ID
  async update(req, res) {
    try {
      const { id } = req.params;
      const data = req.body;
      
      console.log("Update data:", data);
      
      const updated = await Gallery.update(id, data);
      res.json(updated);
    } catch (err) {
      console.error("Update gallery error:", err);
      res.status(500).json({ error: err.message });
    }
  },

  // Delete gallery by ID
  async delete(req, res) {
    try {
      const { id } = req.params;
      console.log("Deleting gallery with ID:", id);

      await Gallery.delete(id);
      res.json({ message: "Gallery deleted successfully", id });
    } catch (err) {
      console.error("Delete gallery error:", err);
      res.status(500).json({ error: err.message });
    }
  },
};

module.exports = galleryController;
