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

// Create image
router.post("/", upload.array("images", 10), galleryController.create);
// Get all images
router.get("/", galleryController.getAll);
// Get image by ID
router.get("/:id", galleryController.getById);
// Update image by ID
router.put("/:id", galleryController.update);
// Delete image by ID
router.delete("/:id", galleryController.delete);

module.exports = router;
