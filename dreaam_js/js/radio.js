function unsetRadio(radioName) {
  const radios = document.getElementsByName(radioName);
  radios.forEach((radio) => console.log(radio.checked)); // Log state of radios

  for (let i = 0; i < radios.length; i++) {
    radios[i].checked = false;
  }
}
