function MVtext() {
    const mvSub = document.querySelectorAll(".mv__content-title");
    const mvContentImg = document.querySelector(".mv__content-img");
    const mvBtn = document.querySelector(".mv__btn");
    const mvReleasePeriod = document.querySelector(".mv__release-period");

    mvSub.forEach(sub => {
        gsap.set(sub, {
            clipPath: "inset(0% 100% 0% 0%)",
            webkitClipPath: "inset(0% 100% 0% 0%)"
        });
    });

    let tl = gsap.timeline();

    mvSub.forEach(sub => {
        tl.to(sub, {
            clipPath: "inset(0% 0% 0% 0%)",
            webkitClipPath: "inset(0% 0% 0% 0%)",
            duration: 0.8,
            ease: "power4.out"
        }, ">");
    });
    tl.to(mvContentImg, {
        y: 0,
        opacity: 1,
        duration: 0.8,
        ease: "power4.out"
    });
    tl.to(mvBtn, {
        opacity: 1,
        duration: 0.5,
        ease: "power4.out",
    })
    tl.to(mvReleasePeriod, {
        scale:1,
        duration: 0.8,
        ease: "power4.out"
    },"1.5")
}


document.addEventListener("DOMContentLoaded", function () {
    MVtext();
})