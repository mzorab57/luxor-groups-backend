const pool = require("../config/database");

class Gallery {
  // Create a new image record
  static async create(data) {
    const [result] = await pool.execute(
      `INSERT INTO gallery (images, qr_code, title, description, category, size, price, sku, orientation, artist_name, upload_date)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        JSON.stringify(data.images),
        data.qr_code || null,
        data.title,
        data.description,
        data.category,
        data.size,
        data.price,
        data.sku,
        data.orientation,
        data.artist_name || null,
        data.upload_date || null,
      ]
    );
    console.log("Insert result:", result);
    return { id: result.insertId, ...data };
  }

  // Get all images
  static async getAll() {
    const [rows] = await pool.execute("SELECT * FROM gallery");
    console.log("rows");
    console.log(rows);

    return rows;
  }

  // Get image by ID
  static async getById(id) {
    const [rows] = await pool.execute("SELECT * FROM gallery WHERE id = ?", [
      id,
    ]);
    return rows[0];
  }

  // Update image by ID
  static async update(id, data) {
    await pool.execute(
      `UPDATE gallery SET images=?, qr_code=?, title=?, description=?, category=?, size=?, price=?, sku=?, orientation=?, artist_name=?, upload_date=? WHERE id=?`,
      [
      JSON.stringify(data.images),
      data.qr_code || null,
      data.title,
      data.description,
      data.category,
      data.size,
      data.price,
      data.sku,
      data.orientation,
      data.artist_name || null,
      data.upload_date || null,
      id,
      ]
    );
    return { id, ...data };
  }

  // Delete image by ID
  static async delete(id) {
    await pool.execute("DELETE FROM gallery WHERE id = ?", [id]);
    return { id };
  }
}

module.exports = Gallery;