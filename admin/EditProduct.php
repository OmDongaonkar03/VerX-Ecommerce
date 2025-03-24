<?php include_once('admin function.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Edit Product - VerX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --primary-light: #534bae;
            --primary-dark: #000051;
            --secondary-color: #f4f6f9;
        }

        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: var(--primary-color);
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding: 1.5rem 1rem;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed h3 span {
            display: none;
        }

        .nav-link {
            color: #fff !important;
            padding: 0.5rem;
			margin: 0 0 6px 0;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            white-space: nowrap;
            opacity: 0.85;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
            opacity: 1;
        }

        .nav-link.active {
            background: rgba(255,255,255,0.2);
            opacity: 1;
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
            transition: all 0.3s;
            width: calc(100% - 250px);
        }

        .main-content.expanded {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        .header {
            background: #212529;
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-card {
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-radius: 15px;
            border: none;
            background: white;
            margin-bottom: 2rem;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
            background: var(--primary-color);
            color: white;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 0.7rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.15);
        }

        .image-preview {
            width: 100%;
            height: 180px;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            transition: all 0.3s;
        }

        .image-preview:hover {
            border-color: var(--primary-light);
            transform: translateY(-3px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #e0e0e0;
            border: none;
            color: #333;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background-color: #d0d0d0;
            transform: translateY(-2px);
        }

        .search-container {
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .product-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 20px;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #eee;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }

        .color-input-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .pagination {
            margin-top: 1rem;
            justify-content: center;
        }

        .page-link {
            color: var(--primary-color);
            border-radius: 8px;
            margin: 0 4px;
        }

        .page-link:hover {
            background-color: var(--primary-light);
            color: white;
        }

        .section-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .add-color-btn, .remove-color-btn {
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .remove-color-btn {
            color: #dc3545;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .sidebar .nav-link span,
            .sidebar h3 span {
                display: none;
            }
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <?php echo sidebar();?>
    </div>

    <div class="main-content" id="main-content">
        <div class="container-fluid py-4">
            <h2 class="section-title mb-4">Manage Products</h2>
            
            <!-- Search Section -->
            <div class="search-container">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search by product name...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select">
                            <option value="">All Categories</option>
                            <option value="men">Men's Clothing</option>
                            <option value="women">Women's Clothing</option>
                            <option value="kids">Kids Clothing</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            Search
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="product-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample row - actual data will be populated by PHP -->
                        <tr>
                            <td><img src="/api/placeholder/60/60" class="product-image" alt="Product"></td>
                            <td>Sample Product Name</td>
                            <td>Men's Clothing</td>
                            <td>$99.99</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- End sample row -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" value="Sample Product" required>
                                <div class="invalid-feedback">Please enter a product name.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" required>
                                    <option value="men">Men's Clothing</option>
                                    <option value="women">Women's Clothing</option>
                                    <option value="kids">Kids Clothing</option>
                                    <option value="accessories">Accessories</option>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" value="99.99" required>
                                    <div class="invalid-feedback">Please enter a valid price.</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="100" class="form-control" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" required>Sample product description</textarea>
                            <div class="invalid-feedback">Please enter a product description.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Colors</label>
                            <div id="colorInputsContainer">
                                <div class="color-input-container d-flex align-items-center gap-2">
                                    <input type="text" class="form-control" name="colors[]" value="Red" required>
                                    <i class="fas fa-plus-circle add-color-btn" onclick="addColorInput()"></i>
                                    <div class="invalid-feedback">Please enter a color name.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                                <div class="invalid-feedback">Please select a status.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" min="0" class="form-control" value="100" required>
                                <div class="invalid-feedback">Please enter a valid quantity.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label mb-2">Product Images</label>
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <input type="file" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview1')">
                                    <div class="image-preview" id="preview1" style="background-image: url('/api/placeholder/180/180')"></div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <input type="file" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview2')">
                                    <div class="image-preview" id="preview2" style="background-image: url('/api/placeholder/180/180')"></div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <input type="file" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview3')">
                                    <div class="image-preview" id="preview3" style="background-image: url('/api/placeholder/180/180')"></div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <input type="file" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview4')">
                                    <div class="image-preview" id="preview4" style="background-image: url('/api/placeholder/180/180')"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" form="editProductForm" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Image preview functionality
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Color input functionality
        function addColorInput() {
            const container = document.getElementById('colorInputsContainer');
            const newInput = document.createElement('div');
            newInput.className = 'color-input-container d-flex align-items-center gap-2';
            newInput.innerHTML = `
                <input type="text" class="form-control" name="colors[]" placeholder="Enter color name" required>
                <i class="fas fa-minus-circle remove-color-btn" onclick="removeColorInput(this)"></i>
                <div class="invalid-feedback">Please enter a color name.</div>
            `;
            container.appendChild(newInput);
        }

        function removeColorInput(element) {
            element.parentElement.remove();
        }

        // Toggle sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }
            
            // Auto-collapse sidebar on small screens
            function checkWidth() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
            }
            
            // Run on load and resize
            checkWidth();
            window.addEventListener('resize', checkWidth);
        });
    </script>
</body>
</html>