export function configureAxiosCSRFToken (vueInstance: Vue, token): void {

}

function setAxiosSCRFToken (): void {
  this.axios.defaults.headers.common['X-CSRF-TOKEN'] = this.getCSRFToken()
  console.log(this.axios.defaults)
}

function setAxiosCSRFTokenIfProduction (): void {
  if (process.env.NODE_ENV === 'production') {
    this.setAxiosSCRFToken()
  }
}

function handleAxiosError (): void {
  this.$swal({
    icon: 'error',
    title: 'Ups...',
    text: 'coś poszło nie tak!'
  })
}

function setAxiosErrorHandler (): void {
  this.axios.interceptors.response.use(res => {
    // is ok
    return res
  }, () => {
    // error
    this.handleAxiosError()
    return {}
  })
}
