export function Card({ children }) {
  return <div className="bg-white shadow-md rounded p-2">{children}</div>;
}

export function CardContent({ children, className }) {
  return <div className={className}>{children}</div>;
}
