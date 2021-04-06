$('.toggle-menu').click(function() {
    $(this).toggleClass('active');
    $('#menu').toggleClass('open');
});


$('.luxurious_wrap .owl-carousel').owlCarousel({
    center: true,
    loop: true,
    items: 5,
    autoplay: true,
    autoplayHoverPause: true,
    margin: 10,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 2
        },
        1170: {
            items: 5
        }
    }
})


$('.explore_seasonal .owl-carousel').owlCarousel({
    center: true,
    loop: true,
    items: 3,
    autoplay: true,
    autoplayHoverPause: true,
    margin: 10,
    slideTransition: 'linear',
    autoplayTimeout: 3000,
    autoplaySpeed: 3000,
    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 1
        },
        1170: {
            items: 3
        }
    }
})

// animation js

gsap.registerPlugin(ScrollTrigger);
// REVEAL //
gsap.utils.toArray(".revealUp").forEach(function(elem) {
    ScrollTrigger.create({
        trigger: elem,
        start: "top 80%",
        end: "bottom 20%",
        markers: false,
        onEnter: function() {
            gsap.fromTo(
                elem, { y: 50, autoAlpha: 0 }, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeave: function() {
            gsap.fromTo(elem, { autoAlpha: 1 }, { autoAlpha: 0, overwrite: "auto" });
        },
        onEnterBack: function() {
            gsap.fromTo(
                elem, { y: -50, autoAlpha: 0 }, {
                    duration: 1,
                    y: 0,
                    autoAlpha: 1,
                    ease: "back",
                    overwrite: "auto"
                }
            );
        },
        onLeaveBack: function() {
            gsap.fromTo(elem, { autoAlpha: 1 }, { autoAlpha: 0, overwrite: "auto" });
        }
    });
});


// delay
gsap.registerPlugin(ScrollTrigger);
gsap.utils.toArray("section").forEach((elem) => {

    let tl = gsap.timeline({
            scrollTrigger: {
                trigger: elem,
                start: 'top 80%',
                end: 'bottom 20%',
                scrub: 1
            },
        })
        .from(elem, { y: 50, duration: 1, ease: 'none' })
});