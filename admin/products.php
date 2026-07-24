<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$page_title = "Manage Products";

// Create upload dir if not exists
if (!is_dir('../uploads/products')) {
    mkdir('../uploads/products', 0777, true);
}

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $cat_id = (int)$_POST['category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $main_image = $_POST['current_image'] ?? '';
    $image_url = trim($_POST['image_url'] ?? '');

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $main_image = time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $main_image);
    } elseif ($image_url !== '') {
        $main_image = $image_url;
    }

    $status = $_POST['status'] ?? 'Inactive';
    $stmt = $conn->prepare("UPDATE products SET category_id = ?, brand_id = ?, name = ?, price = ?, quantity = ?, main_image = ?, is_featured = ?, status = ? WHERE id = ?");
    $stmt->bind_param("iisdisisi", $cat_id, $brand_id, $name, $price, $qty, $main_image, $is_featured, $status, $id);
    $stmt->execute();
    header("Location: products.php");
    exit;
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $cat_id = $_POST['category_id'];
    $brand_id = $_POST['brand_id'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $image_url = trim($_POST['image_url'] ?? '');
    
    // Image Upload or URL
    $img_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $img_name = time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $img_name);
    } elseif ($image_url !== '') {
        $img_name = $image_url;
    }
    
    $stmt = $conn->prepare("INSERT INTO products (category_id, brand_id, name, price, quantity, main_image, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdisis", $cat_id, $brand_id, $name, $price, $qty, $img_name, $is_featured, $status);
    $stmt->execute();
    header("Location: products.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: products.php");
    exit;
}

$products = $conn->query("
    SELECT p.*, c.name as cat_name, b.name as brand_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN brands b ON p.brand_id = b.id 
    ORDER BY p.id DESC
");

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$brands = $conn->query("SELECT * FROM brands ORDER BY name ASC");

$category_list = [];
while ($row = $categories->fetch_assoc()) {
    $category_list[] = $row;
}
$brand_list = [];
while ($row = $brands->fetch_assoc()) {
    $brand_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <?php include '../includes/admin_header.php'; ?>
            
            <div class="content-wrapper">
                
                <div style="display:flex; justify-content:space-between; margin-bottom: 1.5rem;">
                    <div></div>
                    <button class="btn btn-primary" onclick="showModal('addProductModal')">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </button>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Details</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $products->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if($row['main_image']): ?>
                                            <button type="button" class="image-link" onclick="openEditModal(this)"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                                data-category="<?php echo $row['category_id']; ?>"
                                                data-brand="<?php echo $row['brand_id']; ?>"
                                                data-price="<?php echo $row['price']; ?>"
                                                data-quantity="<?php echo $row['quantity']; ?>"
                                                data-status="<?php echo $row['status']; ?>"
                                                data-featured="<?php echo $row['is_featured']; ?>"
                                                data-image="<?php echo htmlspecialchars($row['main_image'], ENT_QUOTES); ?>"
                                            >
                                                <?php $imageSrc = getProductImageSrc($row['main_image']); ?>
                                        <img src="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES); ?>" class="thumb-img" alt="IMG">
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="image-link" onclick="openEditModal(this)"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                                data-category="<?php echo $row['category_id']; ?>"
                                                data-brand="<?php echo $row['brand_id']; ?>"
                                                data-price="<?php echo $row['price']; ?>"
                                                data-quantity="<?php echo $row['quantity']; ?>"
                                                data-status="<?php echo $row['status']; ?>"
                                                data-featured="<?php echo $row['is_featured']; ?>"
                                                data-image=""
                                            >
                                                <div class="thumb-img" style="display:flex; align-items:center; justify-content:center; color:var(--text-light);"><i class="fa-solid fa-image"></i></div>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:600; color:var(--text);"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div style="font-size:0.75rem; color:var(--text-light); margin-top:0.25rem;">
                                            <?php echo htmlspecialchars($row['cat_name']); ?> • <?php echo htmlspecialchars($row['brand_name']); ?>
                                        </div>
                                    </td>
                                    <td><strong>$<?php echo number_format($row['price'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge <?php echo $row['quantity'] > 10 ? 'success' : 'danger'; ?>">
                                            <?php echo $row['quantity']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row['status'] == 'Active'): ?>
                                            <span class="badge success">Active</span>
                                        <?php else: ?>
                                            <span class="badge warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:0.5rem;">
                                            <button type="button" class="btn btn-outline btn-icon" title="Edit" onclick="openEditModal(this)"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                                data-category="<?php echo $row['category_id']; ?>"
                                                data-brand="<?php echo $row['brand_id']; ?>"
                                                data-price="<?php echo $row['price']; ?>"
                                                data-quantity="<?php echo $row['quantity']; ?>"
                                                data-status="<?php echo $row['status']; ?>"
                                                data-featured="<?php echo $row['is_featured']; ?>"
                                                data-image="<?php echo htmlspecialchars($row['main_image'], ENT_QUOTES); ?>"
                                            ><i class="fa-solid fa-pen"></i></button>
                                            <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete this product?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($products->num_rows == 0): ?>
                                <tr><td colspan="6" style="text-align:center; padding:3rem;">No products found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- End Content Wrapper -->
            <?php include '../includes/admin_footer.php'; ?>

            <!-- Add Product Modal -->
            <div id="addProductModal" class="modal">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h3>Add New Product</h3>
                            <button type="button" class="close-btn" onclick="hideModal('addProductModal')"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php $categories->data_seek(0); while($c = $categories->fetch_assoc()): ?>
                                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Brand</label>
                                    <select name="brand_id" class="form-control" required>
                                        <option value="">Select Brand</option>
                                        <?php $brands->data_seek(0); while($b = $brands->fetch_assoc()): ?>
                                            <option value="<?php echo $b['id']; ?>"><?php echo $b['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Price ($)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" name="quantity" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Product Image</label>
                                <input type="file" name="image" id="add_image_input" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="url" name="image_url" id="add_image_url" class="form-control" placeholder="https://example.com/image.jpg">
                                <div id="add_image_preview_wrapper" style="margin-top:0.75rem; display:none;">
                                    <div style="font-size:0.9rem; margin-bottom:0.5rem;">Selected Image Preview</div>
                                    <img id="add_image_preview" src="" alt="Image preview" style="width:100%; max-width:240px; border:1px solid #ddd; padding:0.25rem; border-radius:8px; display:block; margin-bottom:0.5rem;">
                                    <a href="#" id="add_image_link" target="_blank" style="font-size:0.9rem; color:var(--primary);">Open selected image</a>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group" style="display:flex; align-items:center; gap:0.5rem; margin-top:2rem;">
                                    <input type="checkbox" name="is_featured" id="is_featured">
                                    <label for="is_featured" style="margin:0;">Featured Product</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" onclick="hideModal('addProductModal')">Cancel</button>
                            <button type="submit" name="add_product" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div id="editProductModal" class="modal">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <input type="hidden" name="current_image" id="edit_current_image">
                        <div class="modal-header">
                            <h3>Edit Product</h3>
                            <button type="button" class="close-btn" onclick="hideModal('editProductModal')"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" id="edit_category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($category_list as $c): ?>
                                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Brand</label>
                                    <select name="brand_id" id="edit_brand_id" class="form-control" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brand_list as $b): ?>
                                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Price ($)</label>
                                    <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Product Image</label>
                                <input type="file" name="image" id="edit_image_input" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="url" name="image_url" id="edit_image_url" class="form-control" placeholder="https://example.com/image.jpg">
                                <div id="edit_image_preview_wrapper" style="margin-top:0.75rem; display:none;">
                                    <div style="font-size:0.9rem; margin-bottom:0.5rem;">Current Image</div>
                                    <img id="edit_image_preview" src="" alt="Product Image" style="width:100%; max-width:240px; border:1px solid #ddd; padding:0.25rem; border-radius:8px; display:block; margin-bottom:0.5rem;">
                                    <a href="#" id="edit_image_link" target="_blank" style="font-size:0.9rem; color:var(--primary);">Open current image</a>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" id="edit_status" class="form-control">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group" style="display:flex; align-items:center; gap:0.5rem; margin-top:2rem;">
                                    <input type="checkbox" name="is_featured" id="edit_is_featured">
                                    <label for="edit_is_featured" style="margin:0;">Featured Product</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" onclick="hideModal('editProductModal')">Cancel</button>
                            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    function getElement(id) {
                        return document.getElementById(id);
                    }

                    function updatePreview(wrapperId, imgId, linkId, src) {
                        var wrapper = getElement(wrapperId);
                        var img = getElement(imgId);
                        var link = getElement(linkId);
                        if (!wrapper || !img || !link) return;
                        if (!src) {
                            wrapper.style.display = 'none';
                            return;
                        }
                        wrapper.style.display = 'block';
                        img.src = src;
                        link.href = src;
                        link.textContent = 'Open selected image';
                    }

                    var editImageInput = getElement('edit_image_input');
                    var editImageUrl = getElement('edit_image_url');
                    var addImageInput = getElement('add_image_input');
                    var addImageUrl = getElement('add_image_url');

                    if (editImageInput) {
                        editImageInput.addEventListener('change', function(event) {
                            var file = event.target.files[0];
                            if (!file) return;
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                updatePreview('edit_image_preview_wrapper', 'edit_image_preview', 'edit_image_link', e.target.result);
                                if (editImageUrl) editImageUrl.value = '';
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    if (addImageInput) {
                        addImageInput.addEventListener('change', function(event) {
                            var file = event.target.files[0];
                            if (!file) return;
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                updatePreview('add_image_preview_wrapper', 'add_image_preview', 'add_image_link', e.target.result);
                                if (addImageUrl) addImageUrl.value = '';
                            };
                            reader.readAsDataURL(file);
                        });
                    }

                    if (editImageUrl) {
                        editImageUrl.addEventListener('input', function(event) {
                            var url = event.target.value.trim();
                            if (url.match(/^https?:\/\//i)) {
                                updatePreview('edit_image_preview_wrapper', 'edit_image_preview', 'edit_image_link', url);
                                if (editImageInput) editImageInput.value = '';
                            } else if (!url) {
                                updatePreview('edit_image_preview_wrapper', 'edit_image_preview', 'edit_image_link', '');
                            }
                        });
                    }

                    if (addImageUrl) {
                        addImageUrl.addEventListener('input', function(event) {
                            var url = event.target.value.trim();
                            if (url.match(/^https?:\/\//i)) {
                                updatePreview('add_image_preview_wrapper', 'add_image_preview', 'add_image_link', url);
                                if (addImageInput) addImageInput.value = '';
                            } else if (!url) {
                                updatePreview('add_image_preview_wrapper', 'add_image_preview', 'add_image_link', '');
                            }
                        });
                    }

                    window.openEditModal = function(button) {
                        getElement('edit_product_id').value = button.dataset.id;
                        getElement('edit_current_image').value = button.dataset.image;
                        getElement('edit_name').value = button.dataset.name;
                        getElement('edit_category_id').value = button.dataset.category;
                        getElement('edit_brand_id').value = button.dataset.brand;
                        getElement('edit_price').value = button.dataset.price;
                        getElement('edit_quantity').value = button.dataset.quantity;
                        getElement('edit_status').value = button.dataset.status;
                        getElement('edit_is_featured').checked = button.dataset.featured === '1';

                        var currentImage = button.dataset.image;
                        var url = currentImage && currentImage.match(/^https?:\/\//i) ? currentImage : currentImage ? '../uploads/products/' + currentImage : '';
                        updatePreview('edit_image_preview_wrapper', 'edit_image_preview', 'edit_image_link', url);
                        if (editImageUrl) {
                            editImageUrl.value = currentImage && currentImage.match(/^https?:\/\//i) ? currentImage : '';
                        }
                        if (editImageInput) editImageInput.value = '';
                        showModal('editProductModal');
                    };
                });
            </script>
        </div> <!-- End Main Content -->
    </div> <!-- End Admin Layout -->
</body>
</html>
