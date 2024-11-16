var tag_settings_configs = {
	"DIV": {"html":`<div>Div content</div>`},
	"P": {"html":`<p>Paragraph content</p>`},
	"H1": {"html":`<h1>Heading 1</h1>`},
	"H2": {"html":`<h2>Heading 2</h2>`},
	"H3": {"html":`<h3>Heading 3</h3>`},
	"H4": {"html":`<h4>Heading 4</h4>`},
	"BlockQuote": {"html":`<blockquote>quote content</blockquote>`},
	"UL": {"html":`<ul data-block-type="UL"><li>Item 1</li><li>Item 2</li></ul>`},
	"OL": {"html":`<ol data-block-type="UL"><li>Item 1</li><li>Item 2</li></ol>`},
	"Container": {"html":`<div data-block-type="container" class="container" ><p>Container content</p></div>`},
	"Grid": {"html":`<div data-block-type="Grid" class="row" ><div class="col-6">Item 1</div><div class="col-6">Item 2</div></div><div class="row" ><div class="col-6">Item 1</div><div class="col-6">Item 2</div></div>`},
	"CSS Grid": {"html":`<div data-block-type="CSS Grid" class="grid text-center">
			  <div class="g-col-6">.g-col-6</div>
			  <div class="g-col-6">.g-col-6</div>
			  <div class="g-col-6">.g-col-6</div>
			  <div class="g-col-6">.g-col-6</div>
			</div>`},
	"Table": {"html":`<table class="table table-bordered table-striped table-sm" ><tbody><tr><td>Col 1</td><td>Col 2</td><td>Col 3</td></tr><tr><td>Col 1</td><td>Col 2</td><td>Col 3</td></tr></tbody></table>`},

	"IMG": {"html":`<img src="" alt="Image" />`},
	"Figure": {"html":`<figure class="figure">
		  <img src="..." class="figure-img img-fluid rounded" alt="...">
		  <figcaption class="figure-caption">A caption for the above image.</figcaption>
		</figure>`},

	"Static Form": {"html":`<form data-block-type="Static Form" class="needs-validation" novalidate="">
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstName" class="form-label">First name</label>
              <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
              <div class="invalid-feedback">
                Valid first name is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="lastName" class="form-label">Last name</label>
              <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
              <div class="invalid-feedback">
                Valid last name is required.
              </div>
            </div>

            <div class="col-12">
              <label for="username" class="form-label">Username</label>
              <div class="input-group has-validation">
                <span class="input-group-text">@</span>
                <input type="text" class="form-control" id="username" placeholder="Username" required="">
              <div class="invalid-feedback">
                  Your username is required.
                </div>
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Email <span class="text-body-secondary">(Optional)</span></label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com">
              <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
              </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" placeholder="1234 Main St" required="">
              <div class="invalid-feedback">
                Please enter your shipping address.
              </div>
            </div>

            <div class="col-12">
              <label for="address2" class="form-label">Address 2 <span class="text-body-secondary">(Optional)</span></label>
              <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
            </div>

            <div class="col-md-5">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" required="">
                <option value="">Choose...</option>
                <option>United States</option>
              </select>
              <div class="invalid-feedback">
                Please select a valid country.
              </div>
            </div>

            <div class="col-md-4">
              <label for="state" class="form-label">State</label>
              <select class="form-select" id="state" required="">
                <option value="">Choose...</option>
                <option>California</option>
              </select>
              <div class="invalid-feedback">
                Please provide a valid state.
              </div>
            </div>

            <div class="col-md-3">
              <label for="zip" class="form-label">Zip</label>
              <input type="text" class="form-control" id="zip" placeholder="" required="">
              <div class="invalid-feedback">
                Zip code required.
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="same-address">
            <label class="form-check-label" for="same-address">Shipping address is the same as my billing address</label>
          </div>

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="save-info">
            <label class="form-check-label" for="save-info">Save this information for next time</label>
          </div>

          <hr class="my-4" />

          <h4 class="mb-3">Payment</h4>

          <div class="my-3">
            <div class="form-check">
              <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked="" required="">
              <label class="form-check-label" for="credit">Credit card</label>
            </div>
            <div class="form-check">
              <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required="">
              <label class="form-check-label" for="debit">Debit card</label>
            </div>
            <div class="form-check">
              <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required="">
              <label class="form-check-label" for="paypal">PayPal</label>
            </div>
          </div>

          <div class="row gy-3">
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Name on card</label>
              <input type="text" class="form-control" id="cc-name" placeholder="" required="">
              <small class="text-body-secondary">Full name as displayed on card</small>
              <div class="invalid-feedback">
                Name on card is required
              </div>
            </div>

            <div class="col-md-6">
              <label for="cc-number" class="form-label">Credit card number</label>
              <input type="text" class="form-control" id="cc-number" placeholder="" required="">
              <div class="invalid-feedback">
                Credit card number is required
              </div>
            </div>

            <div class="col-md-3">
              <label for="cc-expiration" class="form-label">Expiration</label>
              <input type="text" class="form-control" id="cc-expiration" placeholder="" required="">
              <div class="invalid-feedback">
                Expiration date required
              </div>
            </div>

            <div class="col-md-3">
              <label for="cc-cvv" class="form-label">CVV</label>
              <input type="text" class="form-control" id="cc-cvv" placeholder="" required="">
              <div class="invalid-feedback">
                Security code required
              </div>
            </div>
          </div>

          <hr class="my-4" />

          <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
        </form>`},
	"Input": {"html":`<input type="text" class="form-control" >`},
	"Select": {"html":`<select class="form-select" ><option value="none" >None</option></select>`},
	"TextArea": {"html":`<textarea class="form-control" ></textarea>`},
	"Label": {"html":`<label>Label</label>`},

	"Definition List": {"html": `<div data-block-type="Definition List" class="definition-list" >
		<div class="row" >
			<div class="col-4" >One</div>
			<div class="col-8" >Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</div>
		</div>
		<div class="row" >
			<div class="col-4" >Two</div>
			<div class="col-8" >Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</div>
		</div>
		<div class="row" >
			<div class="col-4" >Three</div>
			<div class="col-8" >Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</div>
		</div>
		<div class="row" >
			<div class="col-4" >Four</div>
			<div class="col-8" >Each of the nine words in the sentence, "The Quick brown fox jumps over the lazy dog" is written on a separate piece of paper.</div>
		</div>
	</div>`},
	"Accordion": {"html": `<div data-block-type="Accordion" class="accordion" id="accordionExample">
		  <div class="accordion-item">
		    <h2 class="accordion-header">
		      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
		        Accordion Item #1
		      </button>
		    </h2>
		    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
		      <div class="accordion-body">
		        <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
		      </div>
		    </div>
		  </div>
		  <div class="accordion-item">
		    <h2 class="accordion-header">
		      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
		        Accordion Item #2
		      </button>
		    </h2>
		    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
		      <div class="accordion-body">
		        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
		      </div>
		    </div>
		  </div>
		  <div class="accordion-item">
		    <h2 class="accordion-header">
		      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
		        Accordion Item #3
		      </button>
		    </h2>
		    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
		      <div class="accordion-body">
		        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
		      </div>
		    </div>
		  </div>
		</div>`},
	"Alert": {"html":`<div class="alert alert-primary" role="alert">
		  A simple primary alert—check it out!
		</div>`},
	"Badge": {"html":`<span class="badge bg-secondary">New</span>`},
	"Breadcrumb": {"html": `<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="#">Home</a></li>
		    <li class="breadcrumb-item"><a href="#">Library</a></li>
		    <li class="breadcrumb-item active" aria-current="page">Data</li>
		  </ol>
		</nav>`},
	"Button": {"html": `<button type="button" class="btn btn-primary">Primary</button>`},
	"Button group": {"html":`<div class="btn-group">
		  <a href="#" class="btn btn-primary active" aria-current="page">Active link</a>
		  <a href="#" class="btn btn-primary">Link</a>
		  <a href="#" class="btn btn-primary">Link</a>
		</div>`},
	"Card": {"html":`<div data-block-type="Card" class="card">
		  <div class="card-header">
		    Featured
		  </div>
		  <div class="card-body">
		    <h5 class="card-title">Special title treatment</h5>
		    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
		    <a href="#" class="btn btn-primary">Go somewhere</a>
		  </div>
		</div>`},
	"ImageCard": {"html":`<div data-block-type="Card" class="card" style="width: 18rem;">
	  <img src="..." class="card-img-top" alt="...">
	  <div class="card-body">
	    <h5 class="card-title">Card title</h5>
	    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
	    <a href="#" class="btn btn-primary">Go somewhere</a>
	  </div>
	</div>`},
	"Carousel": {"html": `<div data-block-type="Carousel" id="carouselExampleCaptions" class="carousel slide">
		  <div class="carousel-indicators">
		    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
		    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
		    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
		  </div>
		  <div class="carousel-inner">
		    <div class="carousel-item active">
		      <img src="..." class="d-block w-100" alt="...">
		      <div class="carousel-caption d-none d-md-block">
		        <h5>First slide label</h5>
		        <p>Some representative placeholder content for the first slide.</p>
		      </div>
		    </div>
		    <div class="carousel-item">
		      <img src="..." class="d-block w-100" alt="...">
		      <div class="carousel-caption d-none d-md-block">
		        <h5>Second slide label</h5>
		        <p>Some representative placeholder content for the second slide.</p>
		      </div>
		    </div>
		    <div class="carousel-item">
		      <img src="..." class="d-block w-100" alt="...">
		      <div class="carousel-caption d-none d-md-block">
		        <h5>Third slide label</h5>
		        <p>Some representative placeholder content for the third slide.</p>
		      </div>
		    </div>
		  </div>
		  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
		    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
		    <span class="visually-hidden">Previous</span>
		  </button>
		  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
		    <span class="carousel-control-next-icon" aria-hidden="true"></span>
		    <span class="visually-hidden">Next</span>
		  </button>
		</div>`},
	"Collapse": {"html":`<div data-block-type="Collapse">
		  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
		    Button with data-bs-target
		  </button>
		</div>
		<div class="collapse" id="collapseExample">
		  <div class="card card-body">
		    Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.
		  </div>
		</div>`},
	"Dropdown": {"html":`<div data-block-type="Dropdown" class="dropdown">
		  <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
		    Dropdown button
		  </button>
		  <ul class="dropdown-menu dropdown-menu-dark">
		    <li><a class="dropdown-item active" href="#">Action</a></li>
		    <li><a class="dropdown-item" href="#">Another action</a></li>
		    <li><a class="dropdown-item" href="#">Something else here</a></li>
		    <li><hr class="dropdown-divider"></li>
		    <li><a class="dropdown-item" href="#">Separated link</a></li>
		  </ul>
		</div>`},
	"List group": {"html":`<ul data-block-type="List Group" class="list-group">
		  <li class="list-group-item" aria-disabled="true">A disabled item</li>
		  <li class="list-group-item">A second item</li>
		  <li class="list-group-item">A third item</li>
		  <li class="list-group-item">A fourth item</li>
		  <li class="list-group-item">And a fifth one</li>
		</ul>`},
	"List Group 2": {"html":`<ol data-block-type="List Group" class="list-group list-group-numbered">
		  <li class="list-group-item d-flex justify-content-between align-items-start">
		    <div class="ms-2 me-auto">
		      <div class="fw-bold">Subheading</div>
		      Content for list item
		    </div>
		    <span class="badge bg-primary rounded-pill">14</span>
		  </li>
		  <li class="list-group-item d-flex justify-content-between align-items-start">
		    <div class="ms-2 me-auto">
		      <div class="fw-bold">Subheading</div>
		      Content for list item
		    </div>
		    <span class="badge bg-primary rounded-pill">14</span>
		  </li>
		  <li class="list-group-item d-flex justify-content-between align-items-start">
		    <div class="ms-2 me-auto">
		      <div class="fw-bold">Subheading</div>
		      Content for list item
		    </div>
		    <span class="badge bg-primary rounded-pill">14</span>
		  </li>
		</ol>`},
	"Modal": {"html":`<div>
	<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
		  Launch demo modal
		</button>
	</div>
	<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
	        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      </div>
	      <div class="modal-body">
	        ...
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary">Understood</button>
	      </div>
	    </div>
	  </div>
	</div>`},
	"Navbar": {"html":`<nav data-block-type="Navbar" class="navbar navbar-expand-lg bg-body-tertiary">
		  <div class="container-fluid">
		    <a class="navbar-brand" href="#">Navbar</a>
		    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		      <span class="navbar-toggler-icon"></span>
		    </button>
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
		        <li class="nav-item">
		          <a class="nav-link active" aria-current="page" href="#">Home</a>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link" href="#">Link</a>
		        </li>
		        <li class="nav-item dropdown">
		          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		            Dropdown
		          </a>
		          <ul class="dropdown-menu">
		            <li><a class="dropdown-item" href="#">Action</a></li>
		            <li><a class="dropdown-item" href="#">Another action</a></li>
		            <li><hr class="dropdown-divider"></li>
		            <li><a class="dropdown-item" href="#">Something else here</a></li>
		          </ul>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
		        </li>
		      </ul>
		      <form class="d-flex" role="search">
		        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
		        <button class="btn btn-outline-success" type="submit">Search</button>
		      </form>
		    </div>
		  </div>
		</nav>`},
	"Navs": {"html":`<ul data-block-type="Navs" class="nav nav-tabs">
		  <li class="nav-item">
		    <a class="nav-link active" aria-current="page" href="#">Active</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link" href="#">Link</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link" href="#">Link</a>
		  </li>
		  <li class="nav-item">
		    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
		  </li>
		</ul>`},
	"Offcanvas": {"html":`<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
		  Button with data-bs-target
		</button>

		<div data-block-type="Offcanvas" class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
		  <div class="offcanvas-header">
		    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
		    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		  </div>
		  <div class="offcanvas-body">
		    <div>
		      Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.
		    </div>
		    <div class="dropdown mt-3">
		      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
		        Dropdown button
		      </button>
		      <ul class="dropdown-menu">
		        <li><a class="dropdown-item" href="#">Action</a></li>
		        <li><a class="dropdown-item" href="#">Another action</a></li>
		        <li><a class="dropdown-item" href="#">Something else here</a></li>
		      </ul>
		    </div>
		  </div>
		</div>`},
	"Pagination": {"html":`<nav data-block-type="Pagination" aria-label="...">
		  <ul class="pagination">
		    <li class="page-item disabled">
		      <a class="page-link">Previous</a>
		    </li>
		    <li class="page-item"><a class="page-link" href="#">1</a></li>
		    <li class="page-item active" aria-current="page">
		      <a class="page-link" href="#">2</a>
		    </li>
		    <li class="page-item"><a class="page-link" href="#">3</a></li>
		    <li class="page-item">
		      <a class="page-link" href="#">Next</a>
		    </li>
		  </ul>
		</nav>`},
	"Scrollspy": {"html":`<nav data-block-type="Scrollspy" id="navbar-example2" class="navbar bg-body-tertiary px-3 mb-3">
		  <a class="navbar-brand" href="#">Navbar</a>
		  <ul class="nav nav-pills">
		    <li class="nav-item">
		      <a class="nav-link" href="#scrollspyHeading1">First</a>
		    </li>
		    <li class="nav-item">
		      <a class="nav-link" href="#scrollspyHeading2">Second</a>
		    </li>
		    <li class="nav-item dropdown">
		      <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Dropdown</a>
		      <ul class="dropdown-menu">
		        <li><a class="dropdown-item" href="#scrollspyHeading3">Third</a></li>
		        <li><a class="dropdown-item" href="#scrollspyHeading4">Fourth</a></li>
		        <li><hr class="dropdown-divider"></li>
		        <li><a class="dropdown-item" href="#scrollspyHeading5">Fifth</a></li>
		      </ul>
		    </li>
		  </ul>
		</nav>
		<div data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" class="scrollspy-example bg-body-tertiary p-3 rounded-2" tabindex="0">
		  <h4 id="scrollspyHeading1">First heading</h4>
		  <p>...</p>
		  <h4 id="scrollspyHeading2">Second heading</h4>
		  <p>...</p>
		  <h4 id="scrollspyHeading3">Third heading</h4>
		  <p>...</p>
		  <h4 id="scrollspyHeading4">Fourth heading</h4>
		  <p>...</p>
		  <h4 id="scrollspyHeading5">Fifth heading</h4>
		  <p>...</p>
		</div>`},
	"RichText": {"html":"<pre>Rich text content</pre>"},
	"DatabaseTable":  {"html":`<app_databasetable data-app="DatabaseTable" >Database Table APP</app_app_databasetable>` },
	"AuthDefault": {"html":`<app_authdefault data-app="AuthDefault" >Default Auth App</app_authdefault>`},
};