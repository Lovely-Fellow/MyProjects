let nextTodoId = 0
export const addTodo = (text) => ({
  type: 'ADD_TODO',
  id: nextTodoId++,
  text
})

export const setVisibilityFilter = (filter) => ({
  type: 'SET_VISIBILITY_FILTER',
  filter
})

export const toggleTodo = (id) => ({
  type: 'TOGGLE_TODO',
  id
})

export const saveTodo = (id, text) => ({
  type: 'SAVE_TODO',
  id,
  text
})
export const editTodo = (id, text, editid, edittext) => ({
  type: 'EDIT_TODO',
  id,
  text,
  editid, 
  edittext
})