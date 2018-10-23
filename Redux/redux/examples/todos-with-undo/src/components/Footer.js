import React from 'react'
import FilterLink from '../containers/FilterLink'

const Footer = () => (
  <p>
    Show:
    {" "}
    <FilterLink filter="SHOW_ALL">
      All
    </FilterLink>
    {", "}
    <FilterLink filter="SHOW_COMPLETED">
      Active
    </FilterLink>
    {", "}
    <FilterLink filter="SHOW_ACTIVE">
      NoActive
    </FilterLink>
  </p>
)

export default Footer
