export function getOuterHeight(element, includeMargin = true) {
  if (!(element instanceof HTMLElement)) {
    return 0;
  }

  const height = element.offsetHeight;

  if (!includeMargin) {
    return height;
  }

  const styles = window.getComputedStyle(element);
  const marginTop = parseFloat(styles.marginTop) || 0;
  const marginBottom = parseFloat(styles.marginBottom) || 0;

  return height + marginTop + marginBottom;
}