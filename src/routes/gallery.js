const express = require("express");
const router = express.Router();
const galleryController = require("../controllers/galleryController");

// Multer setup for file uploads
const multer = require("multer");
const path = require("path");
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, "uploads/");
  },
  filename: function (req, file, cb) {
    cb(null, Date.now() + "-" + file.originalname);
  },
});
const upload = multer({ storage: storage });

// Middleware to handle both form-data and JSON
const handleUpload = (req, res, next) => {
  // Check if it's form-data with files
  if (req.is('multipart/form-data')) {
    upload.array("images", 10)(req, res, next);
  } else {
    // For JSON requests, just continue
    next();
  }
};

// Create image - supports both file upload and URL
router.post("/", handleUpload, galleryController.create);
// Get all images
router.get("/", galleryController.getAll);
// Get image by ID
router.get("/:id", galleryController.getById);
// Update image by ID
router.put("/:id", galleryController.update);
// Delete image by ID
router.delete("/:id", galleryController.delete);

module.exports = router;