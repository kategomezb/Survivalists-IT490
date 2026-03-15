<?php
	if (!isset($_COOKIE['SessionKey'])) { // WEB REFERENCE USED: https://www.geeksforgeeks.org/php/php-cookies/
		header('Location: login.html');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivalists - Dashboard</title>
<!--<link rel="stylesheet" href="/user/style.css"-->
    <style>
        /* I picked this font but we can change it later on. Link that i accessed:
        https://fonts.google.com/specimen/Playfair+Display
        */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');
        /* I did this design because I thought it would look good, but we can change colors and font. I got the background colors from here:
        https://designshack.net/articles/trends/best-website-color-schemes/
        */
        body {
            background-color: #FDF5DF; 
            font-family: 'Playfair Display', serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px 20px;
            /*margin: 0;*/
        }

        .pageWrapper {
            display: flex;
             gap: 30px;
            align-items: flex-start;
            width: 100%
            max-width: 1200px;
            box-sizing: border-box;
            padding: 0 20px;
        }
        /* I made the dashboard box bigger (width) so we have space to add more stuff, but we can change this value later on */
        .dashboard {
            background: white;
            min-width: 0;
            flex: 1; 
            min-height: 450px;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 5px 5px 10px #5EBEC4; /* this gives the shiny effect on the right side */
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .postCard {
            background: white;
            width: 300px;
            flex-shrink: 0;
             padding: 40px;
            border-radius: 15px;
            box-shadow: 5px 5px 10px #5EBEC4;
            position: sticky;
            top: 30px;
        }
        .header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #ffffff;;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .profileUser {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .profileUser img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #F92C85;
            object-fit: cover;
        }
        /* here i might want to add the green dot to confirm that the session is active. */
        .status {
            color: #F92C85;
            font-size: 12px;
            font-weight: bold;
            font-family: 'Plus Jakarta Sans', sans-serif;
            align-self: center;
        }
        h1 {
            margin: 0;
            font-size: 28px;
            color: #000000;
        }
        
        .content {
            flex-grow: 1; /* this part helps to push the logout button to the bottom */
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #000000;
        }

        .content p {
	   margin-top:50px;
        }

        .searchBar {
            display: flex;
            flex-direction: row; 
            flex-wrap: wrap; 
            gap: 10px;
            width: 700px;
            position: absolute;
            top: 145px;  
            left: 50px;
            right:50px;     
        }

        .searchInput {
            padding: 8px;
            width: 50%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
        }

        .searchButton {
            padding: 10px 20px;
            background-color: #5EBEC4;
            color: white;
            border: none;
            border-radius: 9px;
            font-weight: bold;
        }
        .viewMoreButton {
           padding: 5px 15px;
           margin-top: 10px;
            background-color: #5EBEC4;
           color: white;
           border: none;
           border-radius: 5px;
           font-weight: bold;
         }
        .postThisButton {
            padding: 5px 15px;
            margin-top: 10px;
            background-color: #F92C85;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
	.postThisButton:hover { 
           background-color: #d9256f; 
        }
        .searchButton:hover {
            background-color: #4daeb4; 
        }

        .checkBoxes {
            margin-top: -3px;
            width: 100%;
            font-size: 14px;
            display: block;;
        }
         
        .checkBoxes label {
            margin-right: 15px;
            cursor: pointer;
        }
        
        .logoutButton {
            align-self: flex-start;
            margin-top: 20px;
            text-decoration: none;
            color: #F92C85;
            font-weight: bold;
            font-size: 14px;
        }
        .logoutButton:hover {
            text-decoration: underline;
        }

        #results{
	width:100%;
	font-family:'Plus Jakarta Sans', sans-serif;
        }

        .resultCard{
	   background:#FDF5DF;
	   border-left:5px solid #5EBEC4;
	   padding:12px;
	   margin-bottom:10px;
	   border-radius:6px;
         }
           // here i want tht the card selected can be highligted
        .resultCard.selected{
           border-left-color:#F92C85;
           background:#fff0f6;
        }
        #selectedMediaPreview{
           background:#FDF5DF;
           border-left:4px solid #F92C85;
           padding:12px;
           margin-bottom:15px;
            font-size:13px;
           font-family:'Plus Jakarta Sans', sans-serif;
        }
        #postContent{
           width:100%;
           padding:8px;
           border:1px solid #626262;
           border-radius:5px;
           font-family:'Plus Jakarta Sans', sans-serif;
            margin-bottom:10px;
            min-height:80px;
            box-sizing:border-box;
            resize: none;
           outline: none;
        }
        #submitPostBtn{
            width:auto;
            padding:10px 30px;
           background-color:#5EBEC4;
           color:white;
            border:none;
           border-radius:9px;
           font-weight:bold;
           display:block;
           margin:0 auto;
        }
        #submitPostBtn:disabled{
           background-color:#D3D3D3;
        }
        #postFeedback{
           margin-top:10px;
           font-size:13px;
           font-family:'Plus Jakarta Sans', sans-serif;
           text-align:center;
        }
        .postCard h2{
           text-align:center;
        }
        .feedbackSuccess{ 
	   color:green; 
        }
        .feedbackError{ 
           color:red; 
         }
    </style>
</head>
<body>
    <div class="pageWrapper">
    <div class="dashboard">
        <div class="header">
            <div class="profileUser">
                <!-- i got the image from here: https://unsplash.com/photos/collection-of-various-music-album-covers-998pvuxqK6Y -->
                <img src="images/dashboardImage.jpg" alt="User">
                <h1>Welcome back to SocialTune!</h1>
            </div>
            
        </div>
        <div class="content">
         <!--   <p>This is our dashboard. I made the box extra big so there's actually room for the stuff we're supposed to add. 
               Remember that we can change everything if you don't like it.</p> -->

            <form class="searchBar" id="searchForm">
                <input type="text" name="userInput" class="searchInput" placeholder="Search for songs, albums..." required>
                <button type="submit" class="searchButton">Find</button>

                <div class="checkBoxes">
                    <span>Filter by: </span>
                    <label><input type="checkbox" name="userFilters[]" value="artists" checked> Artists</label>
                    <label><input type="checkbox" name="userFilters[]" value="albums"> Albums</label>
                    <label><input type="checkbox" name="userFilters[]" value="tracks"> Tracks</label>
                </div>
            </form>
            <div id="results"></div>
        </div>
        <!-- I added the arrow effect on the login, register and dashboard because i saw it in one website and i thought it looked good and modern. I got the link from:
        https://www.w3schools.com/charsets/ref_utf_arrows.asp -->
        <a href="userProfile.php" class="logoutButton">Back to Profile &rarr;</a>
    </div>

    <!-- this will be the right card where the user can post-->
	<div class="postCard">
            <h2>Create a Post</h2>
            <div id="selectedMediaPreview">
                <div class="previewEmpty">Select an artist, album or track to post about.</div>
            </div>
            <textarea id="postContent" placeholder="Share your thoughts..."></textarea>
            <!-- Hiding the button so they dont press it before the select something to post
		    my reference was: https://www.w3schools.com/Tags/att_button_disabled.asp-->
            <button id="submitPostBtn" disabled>Post</button>
            <div id="postFeedback"></div>
        </div>
    </div>
</div>
</body>
</html>


<script>

let pickedItem = null;

// here im trying to select all the checkboxes inputs that are on the userFilters.
// To understand querySelectorAll i refrenced: https://developer.mozilla.org/en-US/docs/Web/API/Document/querySelectorAll
document.querySelectorAll("input[name='userFilters[]']").forEach(cb => {

    // this listener will run everytime a checkbox is changed. 
    cb.addEventListener("change", function () {

        if (this.checked) {
	    // for now i just want one filter can be selected.
            document.querySelectorAll("input[name='userFilters[]']").forEach(other => {
                if (other !== this) {
                    other.checked = false;
                }
            });
        }

    });
});

// this will hide or show information for the result, and it will get the id so it can togle
function toggleDetails(id){

    // gettig the element that has all the details. 
    // i used this refrence to understan better the getElementById: https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementById 
    let el = document.getElementById("details-"+id);

    if(el.style.display === "none"){
        el.style.display = "block";
    }else{
        el.style.display = "none";
    }

}

// added this new func to handle  the right part
function pickItem(itemData, cardIndex) {

    // my ref for the parse https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse
     pickedItem = JSON.parse(decodeURIComponent(itemData));
 
    document.querySelectorAll(".resultCard").forEach(card => {
        card.classList.remove("selected");
    });
    document.getElementById("card-" + cardIndex).classList.add("selected");
     let name = pickedItem.name ?? pickedItem.title; 
    document.getElementById("selectedMediaPreview").innerHTML = `
        <b>${name}</b><br>
         
        <span style="color:#888;font-size:12px;">${pickedItem.type} · ID: ${pickedItem.id}</span>
    `;
 
    // this lets the button to be available when our select something 
    document.getElementById("submitPostBtn").disabled = false;
    document.getElementById("postFeedback").textContent = "";
}

// i used this this link to understand the EventListener better. Which allowed me to listen for the submit even when the user seachr something
//REF: https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener
document.getElementById("searchForm").addEventListener("submit", function(e){

	e.preventDefault();
   // here i try to get what the user typed.
    // REF: https://developer.mozilla.org/en-US/docs/Web/API/Document/querySelector
	const searchValue = document.querySelector(".searchInput").value;

	const filters = [];
// i used those links so i can understand better and i can implement a loopthat goes through all the checkboxes
//REF: https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach AND https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/:checked
	document.querySelectorAll("input[name='userFilters[]']:checked").forEach(cb=>{
		filters.push(cb.value);
	});
// using fetch here helps me to send the req to the php file
// REF: https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
	fetch("searchBar.php",{
		method:"POST",
		headers:{
			"Content-Type":"application/json"
		},

                // here i used this ref so i can  convert the data to json so i can send it to the php file 
		// REF: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/stringify
		body:JSON.stringify({
			artist:searchValue,
			filters:filters
		})
	})

     // response allows me to get the resonse thats on the server, and changed to json
     // REEF: https://developer.mozilla.org/en-US/docs/Web/API/Response/json
	.then(res=>res.json())
	.then(data=>{
                let artists = data.filter(item => item.type === 'artist');
                let others = data.filter(item => item.type !== 'artist');

                artists.sort((a,b) => (b.popularity ?? 0) -(a.popularity ?? 0));

                let sortedData = [...artists, ...others]; 
		let html="";
               
		sortedData.forEach((item,index)=>{
            //since some of the results can be artist or album/track, i added the  ?? so it can display whatever that is there
           // REF: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Nullish_coalescing
			let name=item.name ?? item.title;
                        let link = '';
                        let popularity = '';
                        let noOfVolume = '';
                        let noOfItem = '';
      
                         // added link to open in tidal
                        if (item.type == 'track'){
                        link = `<a href="https://tidal.com/track/${item.id}" target="_blank">Listen on Tidal</a>`; 
}
                       if (item.type == 'artist') {
                        link = `<a href="https://tidal.com/artist/${item.id}" target="_blank">Open in Tidal</a>`;
                        popularity = "<b>Popularity:</b> " + item.popularity + "<br>"; 
}
                       if (item.type == 'album'){
                        link = `<a href="https://tidal.com/album/${item.id}" target="_blank">Open in Tidal</a>`;
                        noOfVolume = "<b>No of Volumes: </b> " + item.no_of_volume + "<br>";
                        noOfItem = "<b>No of Items: </b>" + item.no_of_item + "<br>";
}
                       let encode = encodeURIComponent(JSON.stringify(item));
          // understandind the literals, helped me to build the card for the results
          // REF: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
			html += `
                <div class="resultCard" id="card-${index}">
                    <b>${name}</b><br>
                    ID: ${item.id}<br>

                    <button onclick="toggleDetails(${index})" class="viewMoreButton">View More</button>
                    <button onclick="pickItem('${encoded}', ${index})" class="postThisButton">Post This</button>
                   <!-- this section will display the info that i hid-->
                    <div id="details-${index}" style="display:none;margin-top:10px;">
                        ${popularity}
                        ${noOfVolume}
                        ${noOfItem}
                        ${link}
                    </div>
                </div>
            `;

		});

                   // here i reset the right part when there is a new search
		       pickedItem = null;
                       document.getElementById("submitPostBtn").disabled = true;
                       document.getElementById("selectedMediaPreview").innerHTML =
                    '<div class="previewEmpty">Select an artist, album or track to post about.</div>';
                 // adding the results to the page
		document.getElementById("results").innerHTML=html;

	});

});

// here i hando everything about  the button post
document.getElementById("submitPostBtn").addEventListener("click", function() {
 
    let caption  = document.getElementById("postContent").value.trim();
    let feedback = document.getElementById("postFeedback");
 
    if (!pickedItem) {
        feedback.textContent = "You need to select a media item first.";
        feedback.className = "feedbackError";
        return;
    }
    if (!caption) {
        feedback.textContent = "You need to add a caption before posting.";
        feedback.className = "feedbackError";
        return;
    }
     // when the req is happening this wil disable it. REF for textContent: https://developer.mozilla.org/en-US/docs/Web/API/Node/textContent
    this.disabled = true;
    feedback.textContent = "Posting...";
    feedback.className = "";
 
    // REF: https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
    fetch("createPostRequest.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            media:    pickedItem,
            content:  caption,
            postedAt: Math.floor(Date.now() / 1000) // ref for timestamp on js https://stackoverflow.com/questions/221294/how-do-i-get-a-timestamp-in-javascript
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.returnCode === "0") {
            feedback.textContent = "it was posted successfully!";
            feedback.className = "feedbackSuccess";
 
            // here i clean the the right for so it is good for the next ine
            document.getElementById("postContent").value = "";
            pickedItem = null;
            document.getElementById("selectedMediaPreview").innerHTML =
                '<div class="previewEmpty">Select an artist, album or track to post about.</div>';
            document.querySelectorAll(".resultCard").forEach(card => {
                card.classList.remove("selected");
            });
            this.disabled = true;
 
        } else {
            feedback.textContent = "Error: " + (response.message ?? "Something went wrong.");
            feedback.className = "feedbackError";
            this.disabled = false;
        }
    })
});



</script>
