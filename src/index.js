const express = require("express");
const dotenv = require("dotenv");
const cors = require("cors");

const galleryRoutes = require("./routes/gallery");

dotenv.config();

const app = express();
const PORT = process.env.PORT || 5000;

// Middleware
app.use(cors());
app.use(express.json());
app.use('/uploads', express.static('uploads'));

// Route
app.use("/api/gallery", galleryRoutes);

app.listen(PORT, () => {
  console.log(`✅ Server running on http://localhost:${PORT}`);
});