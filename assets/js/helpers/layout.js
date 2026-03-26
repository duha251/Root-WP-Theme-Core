export function setMainMinHeight() {
  const header = document.querySelector("#headerDesktop:not(.header-transparent)");
  const footer = document.querySelector("#footer");
  const hero   = document.querySelector("#hero");
  const main   = document.querySelector("#main");

  const mainContentHeight =
    window.innerHeight -
    (
      header.offsetHeight +
      footer.offsetHeight +
      hero.offsetHeight
    );

  if (main && mainContentHeight > 0) {
    main.style.minHeight = `${mainContentHeight}px`;
  }
}