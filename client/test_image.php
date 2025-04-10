   <form method="POST" enctype="multipart/form-data" action="https://ucscucbs.net/client_profile_page.php">
       <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
       <div class="row ">
           <div class="col-lg-4 mb-3">
               <div class="mb-3">
                   <label class="text-label form-label">Client's Passport-Sized
                       Photo</label>
                   <input type="file" name="photo" class="form-control" placeholder="" >
               </div>
           </div>

           <div class="col-lg-4 mb-3">
               <div class="mb-3">
                   <label class="text-label form-label">Client's Scanned
                       Signature</label>
                   <input type="file" name="sign" class="form-control" placeholder="" >
               </div>
           </div>

           <div class="col-lg-4 mb-3">
               <div class="mb-3">
                   <label class="text-label form-label">Any Other
                       Attachments</label>
                   <input type="file" name="otherattach" class="form-control" placeholder="" >
               </div>
           </div>

           <div class="col-lg-4 mb-3">
               <div class="mb-3">
                   <label class="text-label form-label">FingerPrint Enrollment</label>
                   <input type="file" name="fingerprint" class="form-control" placeholder="" >
               </div>
           </div>
           <div class="col-lg-4 mb-3">
               <div class="mb-3">
                  
                       <div class="sweetalert mt-5">
                           <button type="submit" name="submitc" class="btn btn-primary btn sweet-confirm">Update
                               Attachments</button>
                       </div>

               </div>
           </div>

       </div>
   </form>