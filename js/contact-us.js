// Base path prefix — contact page is inside pages/, one level deeper
const BASE = '../';

fetch(BASE + "components/nav.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("nav").innerHTML = data;
    const logo = document.querySelector(".nav-logo img");
    if (logo) logo.src = BASE + "assets/images/white-cloud.png";
    // Fix nav links for pages/ context
    document.querySelectorAll('.nav-items a, .contact-btn').forEach(a => {
      const href = a.getAttribute('href');
      if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('../')) {
        a.setAttribute('href', BASE + href);
      }
    });
})
.catch(error => console.log("Error loading navbar:", error));

fetch(BASE + "forms/contact-form.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("contact").innerHTML = data;
})
.catch(error => console.log("Error loading contact form:", error));

fetch(BASE + "components/footer.html")
.then(response=>response.text())
.then((data)=>{
    document.getElementById("footer").innerHTML = data;
    // Fix footer logo path for pages/ context
    const footerLogo = document.querySelector(".footer__logo-img");
    if (footerLogo) footerLogo.src = BASE + "assets/images/white-cloud.png";
})
.catch(error => console.log("Error loading footer:", error));
