<?php 
include('../middleware/adminMiddleware.php');
include('includes/header.php'); 


?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if(isset($_GET['id']))
            {
                $id = $_GET['id'];
                $category = getByID("categories", $id);
                
                if(mysqli_num_rows($category) > 0)
                {
                    $data = mysqli_fetch_array($category);
                    ?>
                        <div class="card">
                                <div class="card-header" style="background-color: rgb(220,53,69);">
                                    <h4 style="color:white;">Edit a Category</h4>
                                    <a href="category.php" class="btn float-end" style="background-color: rgb(255,193,7); color:black;">Back</a>
                                    <p class="text-white mb-0">
                                        Our "Edit a Category" admin page provides you with complete control over the structure of your product categories. 
                                        Whether you need to update an existing category, create a new one, or make adjustments to the hierarchy, this page empowers you to customize your website's navigation and improve the overall user experience.
                                    </p>
                                </div>
                               
                            <div class="card-body">
                                <form action="code.php" method="POST" enctype="multipart/form-data">
                                    <div class="row"> 
                                        <div class="col-md-6">
                                            <input type="hidden" name="category_id" value = <?=$data['id']?> >
                                            <label for=" " style="margin-bottom:20px;">Name</label>
                                            <input type="text" name = "name" value = <?=$data['name']?> placeholder = "Enter desired category" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for=" " style="margin-bottom:20px;">Slug</label>
                                            <input type="text" name = "slug" value = <?=$data['slug']?> placeholder = "Enter slug" class="form-control">
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                            <label for=" " style="margin-top:20px;">Description</label>
                                            <textarea rows ="3" name="description" placeholder="Enter description" class="form-control"><?=$data['description']?> </textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label for=" " style="margin-top:20px;">Upload Image</label>
                                            <input type="file" name = "image" placeholder = "Enter image" class="form-control">
                                            <label for=" " style="margin-bottom:20px;">Current Image</label>
                                            <input type="hidden" name="old_image" value="<?$data['image']?>">
                                            <img src="uploads/<?=$data['image'] ?>" height = "50px" width = "50px" alt="<?=$data['name']?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label for=" " style="margin-top:20px;">Meta Title</label>
                                            <input type="text" name = "meta_title" value = <?=$data['meta_title']?> placeholder = "Enter meta title" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label for=" " style="margin-top:20px;">Meta Description</label>
                                            <textarea rows ="3" name = "meta_description" placeholder = "Enter meta description" class="form-control"><?=$data['meta_description']?> </textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label for=" " style="margin-top:20px;">Meta Keywords</label>
                                            <textarea rows ="3" name = "meta_keywords" placeholder = "Enter meta keywords" class="form-control"><?=$data['meta_keywords']?> </textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for=" " style="margin-top:15px;">Private</label>
                                            <input type="checkbox" <?=$data['status'] ? "checked": ""?> name = "status">
                                        </div>
                                        <div class="col-md-6">
                                            <label for=" " style="margin-top:20px;">Trending</label>
                                            <input type="checkbox" <?=$data['popular'] ? "checked": ""?> name = "trending">
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary" name="update_category_btn" style="background-color: rgb(255,193,7); color:black;">Update Category</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>    
                    <?php
                }
                
                else
                {
                    echo "Category not found.";
                }

            }
            else
            {
                echo "ID Missing from URL";
            }

                    ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>